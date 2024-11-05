<?php

namespace App\Http\Controllers;

use App\Models\ParametroMail;
use Illuminate\Http\Request;

class ConfiguracionMailController extends Controller
{
    public function edit()
    {
       
        $configuracion_mail = ParametroMail::first();
        
        
        return view('sistema.edit', compact('configuracion_mail'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'host' => 'required|string',
            'puerto' => 'required|integer',
            'usuario' => 'required|string',
            'clave' => 'required|string',
        ]);

        
        $configuracion_mail = ParametroMail::first();

        
        if (!$configuracion_mail) {
            $configuracion_mail = new ParametroMail();
        }

        
        $configuracion_mail->host = $request->host;
        $configuracion_mail->puerto = $request->puerto;
        $configuracion_mail->usuario = $request->usuario;
        $configuracion_mail->clave = $request->clave;
        $configuracion_mail->fecha_sistema = now();
        $configuracion_mail->save();

        return redirect()->back()->with('success', 'Parámetros SMTP actualizados correctamente.');
    }
}
