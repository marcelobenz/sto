<?php

namespace App\Http\Controllers;

use App\Models\ContribuyenteMultinota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class IngresoExternoController extends Controller
{
    public function showLoginForm()
    {
        return view('externo.ingreso-externo');
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
}
