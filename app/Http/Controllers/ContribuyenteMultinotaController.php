<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\ContribuyenteMultinota;
use App\Mail\RestablecerClaveMailable;

class ContribuyenteMultinotaController extends Controller
{
    
    public function index()
    {
        return view('contribuyentes.index'); 
    }

   
    public function buscar(Request $request)
    {
        
        $request->validate([
            'cuit' => 'required|digits:11', 
        ]);

       
        $contribuyente = ContribuyenteMultinota::where('cuit', $request->cuit)->first();

        
        if (!$contribuyente) {
            return back()->with('error', 'No se encontró un contribuyente con ese CUIT.');
        }

       
        return view('contribuyentes.index', ['contribuyente' => $contribuyente]); 
    }


    public function actualizarCorreo(Request $request, $id)
    {
        $request->validate([
            'correo' => 'required|email'
        ]);

       
        $contribuyente = ContribuyenteMultinota::where('id_contribuyente_multinota', $id)->firstOrFail();
        $contribuyente->correo = $request->input('correo');
        $contribuyente->save();

        return redirect()->back()->with('success', 'Correo actualizado correctamente');
    }



    public function restablecerClave($id)
{
    
    $newPassword = Str::random(12); 

    
    $encryptedPassword = Hash::make($newPassword);

    
    $contribuyente = ContribuyenteMultinota::where('id_contribuyente_multinota', $id)->firstOrFail();
    $contribuyente->clave = $encryptedPassword;
    $contribuyente->save();

    Mail::to($contribuyente->correo)->send(new RestablecerClaveMailable($newPassword));

    
    return redirect()->back()->with('success', "La nueva contraseña es: $newPassword");
}
}
