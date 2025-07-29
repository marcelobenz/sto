<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\ContribuyenteMultinota;
use App\Models\Direccion; 
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use DB;
use Log;


class IngresoExternoController extends Controller
{
   
    public function showLoginForm()
    {
        return view('externo.ingreso-externo');
    }


      public function registro()
    {
        return view('externo.registro');
    }

public function bandejaExterna(Request $request)
{
    if ($request->ajax()) {
        $usuario = session('contribuyente_multinota');
        $cuitLogueado = $usuario ? $usuario->cuit : null;

        $tramites = DB::table('tramite as t')
            ->join('multinota as m', 't.id_tramite', '=', 'm.id_tramite')
            ->join('tramite_estado_tramite as tet', 'tet.id_tramite', '=', 't.id_tramite')
            ->join('tipo_tramite_multinota as ttm', 'ttm.id_tipo_tramite_multinota', '=', 'm.id_tipo_tramite_multinota')
            ->join('categoria as c', 'ttm.id_categoria', '=', 'c.id_categoria')
            ->leftJoin('usuario_interno as u', 'u.id_usuario_interno', '=', 'tet.id_usuario_interno')
            ->select(
                't.id_tramite', 
                't.fecha_alta', 
                't.fecha_modificacion', 
                't.correo', 
                't.cuit_contribuyente', 
                'c.nombre as nombre_categoria'
            )
            ->distinct()
            ->orderBy('t.id_tramite', 'DESC');

    
        if ($cuitLogueado) {
            $tramites->where('t.cuit_contribuyente', $cuitLogueado);
        } else {
            return response()->json([], 403);
        }

        Log::info('Consulta SQL ejecutada: ' . $tramites->toSql());

        $registros = $tramites->get();

        if ($registros->isEmpty()) {
            Log::info('No se encontraron registros');
        } else {
            Log::info('Registros encontrados', ['count' => $registros->count()]);
        }

        return DataTables::of($registros)->make(true);
    }

    return view('externo.bandeja-usuario-externo');
}



    
    public function login(Request $request)
    {
        $request->validate([
            'cuit' => 'required|string',
            'clave' => 'required|string',
        ]);
    
        $cuit = $request->input('cuit');
        $clave = $request->input('clave');
    
        
        $usuario = ContribuyenteMultinota::where('cuit', $cuit)->first();
    
        if ($usuario && Hash::check($clave, $usuario->clave)) {
           
            session(['contribuyente_multinota' => $usuario]);
            session(['isExterno' => true]);
    
           
            return redirect()->route('bandeja-usuario-externo');
        } else {
            return back()->withErrors([
                'error' => 'CUIT o contraseña incorrectos.',
            ]);
        }
    }


public function registrar(Request $request)
{
    $request->validate([
        'cuit' => 'required|string|unique:contribuyente_multinota,cuit',
        'correo' => 'required|email|unique:contribuyente_multinota,correo',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'telefono_1' => 'nullable|string|max:20',
        'telefono_2' => 'nullable|string|max:20',
        'clave' => 'required|string|min:6|confirmed',

        'calle' => 'required|string|max:255',
        'numero' => 'required|string|max:20',
        'codigo_postal' => 'nullable|string|max:20',
        'provincia' => 'nullable|string|max:255',
        'localidad' => 'nullable|string|max:255',
        'pais' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    $direccion = new Direccion();
    $direccion->calle = $request->calle;
    $direccion->numero = $request->numero;
    $direccion->codigo_postal = $request->codigo_postal;
    $direccion->provincia = $request->provincia;
    $direccion->localidad = $request->localidad;
    $direccion->pais = $request->pais;
    $direccion->latitud = $request->latitude;
    $direccion->longitud = $request->longitude;
    $direccion->fecha_alta = now();
    $direccion->save();

    $usuario = new ContribuyenteMultinota();
    $usuario->cuit = $request->cuit;
    $usuario->correo = $request->correo;
    $usuario->nombre = $request->nombre;
    $usuario->apellido = $request->apellido;
    $usuario->telefono1 = $request->telefono_1;
    $usuario->telefono2 = $request->telefono_2;
    $usuario->clave = Hash::make($request->clave);
    $usuario->id_direccion = $direccion->id_direccion;
    $usuario->save();

    session(['contribuyente_multinota' => $usuario]);
    session(['isExterno' => true]);

    return redirect()->route('bandeja-usuario-externo');
}

    
}
