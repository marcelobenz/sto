<?php

namespace App\Http\Controllers;

use App\Models\UsuarioInterno;
use App\Models\Licencia;
use Illuminate\Http\Request;

class LicenciaController extends Controller
{
    public function crearLicencia($id_usuario_interno)
    {
        $usuario = UsuarioInterno::findOrFail($id_usuario_interno);
        $historialLicencias = Licencia::where('id_usuario_interno', $id_usuario_interno)->get(); // Obtener todas las licencias del usuario
        
        return view('licencias.edit', compact('usuario', 'historialLicencias'));
    }
    
    public function guardarLicencia(Request $request, $id_usuario_interno)
    {
        // Validación de los datos
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'required|string|max:255',
        ]);
    
        // Crear una nueva licencia
        $licencia = new Licencia();
        $licencia->id_usuario_interno = $id_usuario_interno;
        $licencia->motivo = $request->input('motivo');
        $licencia->fecha_inicio = $request->input('fecha_inicio');
        $licencia->fecha_fin = $request->input('fecha_fin');
        $licencia->save();
    
        // Redireccionar con un mensaje de éxito
        return redirect()->route('licencias.crear', $id_usuario_interno)
                         ->with('success', 'Licencia creada exitosamente');
    }
    
}
