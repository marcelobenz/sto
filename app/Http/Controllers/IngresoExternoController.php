<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ContribuyenteMultinota;
use App\Models\Direccion; 

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

    public function showBandeja()
    {
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
