<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\ContribuyenteMultinota;
use App\Models\Direccion; 
use App\Services\IngresoExternoService;
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

     public function __construct(IngresoExternoService $ingresoExternoService)
    {
        $this->ingresoExternoService = $ingresoExternoService;
    }

 public function bandejaExterna(Request $request)
    {
        if ($request->ajax()) {
            $usuario = session('contribuyente_multinota');
            $cuitLogueado = $usuario ? $usuario->cuit : null;

            if (!$cuitLogueado) {
                return response()->json([], 403);
            }

            $registros = $this->ingresoExternoService->obtenerTramitesParaCuit($cuitLogueado);


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

        $usuario = $this->ingresoExternoService->login($request);

        if ($usuario) {
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

    $this->ingresoExternoService->registrarUsuario($request);

    return redirect()->route('bandeja-usuario-externo');
}

    
}
