<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContribuyenteMultinota;

class ContribuyenteMultinotaController extends Controller
{
    // Mostrar la página con la caja de texto
    public function index()
    {
        return view('contribuyentes.index'); 
    }

    // Procesar la búsqueda
    public function buscar(Request $request)
    {
        // Validar que se ha ingresado un CUIT
        $request->validate([
            'cuit' => 'required|digits:11', // CUIT debe tener 11 dígitos
        ]);

        // Buscar el contribuyente en la base de datos
        $contribuyente = ContribuyenteMultinota::where('cuit', $request->cuit)->first();

        // Si no se encuentra, devolver un mensaje de error
        if (!$contribuyente) {
            return back()->with('error', 'No se encontró un contribuyente con ese CUIT.');
        }

        // Si se encuentra, devolver los datos del contribuyente
        return view('contribuyentes.index', ['contribuyente' => $contribuyente]); // Cambiado a 'index'
    }
}
