<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\ContribuyenteMultinota;
use App\Mail\RestablecerClaveMailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ContribuyenteMultinotaController extends Controller
{
    
    public function index()
    {
        return view('contribuyentes.index'); 
    }

    public function showChangePasswordForm()
    {
        return view('externo.cambiar-clave');
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


public function changePassword(Request $request)
{
    
    Log::info('Inicio del proceso de cambio de contraseña.');

    $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed', 
    ]);
    Log::info('Solicitud validada correctamente.');

   
    $contribuyente = Session::get('contribuyente_multinota');
    Log::info('Contribuyente obtenido de la sesión: ', ['contribuyente' => $contribuyente]);
    Log::info('Tipo de objeto en la sesión: ', ['tipo' => get_class($contribuyente)]);

    
    if (!$contribuyente) {
        Log::warning('El contribuyente no está autenticado.');
        return back()->withErrors(['error' => 'Usuario no autenticado.']);
    }

    
    if (!($contribuyente instanceof ContribuyenteMultinota)) {
        Log::error('El objeto contribuyente no es una instancia válida de ContribuyenteMultinota.');
        return back()->withErrors(['error' => 'El usuario no es válido.']);
    }

    
    if (!Hash::check($request->current_password, $contribuyente->clave)) {
        Log::warning('La contraseña actual proporcionada es incorrecta.');
        return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
    }

    
    $contribuyente->clave = Hash::make($request->new_password);
    $contribuyente->save();  
    Log::info('Contraseña actualizada correctamente para el contribuyente: ', ['cuit' => $contribuyente->cuit]);

    
    $contribuyente->clave = null; // Limpiar la clave antes de guardar
    Session::put('contribuyente_multinota', $contribuyente);
    Log::info('Clave del contribuyente eliminada de la sesión antes de guardarla.');

    
    Log::info('Proceso de cambio de contraseña finalizado exitosamente.');
    return redirect()->route('bandeja-usuario-externo')->with('success', 'Contraseña cambiada con éxito.');
}





}
