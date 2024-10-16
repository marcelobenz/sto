<?php

namespace App\Http\Controllers;

use App\Models\GrupoInterno;
use Illuminate\Http\Request;
use App\Models\UsuarioInterno;

class LimiteController extends Controller
{
    public function index()
    {
        $grupos = GrupoInterno::with('usuarios')->get();
        return view('limites.index', compact('grupos'));
    }


    public function guardarLimite(Request $request)
    {
        try {
            $request->validate([
                'limites' => 'required|array',
                'limites.*.id_usuario_interno' => 'required|integer|exists:usuario_interno,id_usuario_interno',
                'limites.*.limite' => 'required|numeric|min:0',
            ]);
    
            foreach ($request->limites as $limiteData) {
                
                $usuario = UsuarioInterno::find($limiteData['id_usuario_interno']);
                if ($usuario) {
                    
                    $usuario->limite = $limiteData['limite'];
              
                    $usuario->update(); 
    
                    \Log::info("Límite guardado exitosamente para usuario ID: " . $usuario->id_usuario_interno);
                } else {
                    \Log::error('Usuario no encontrado', ['id' => $limiteData['id_usuario_interno']]);
                }
            }
            return response()->json(['message' => 'Límites guardados exitosamente']);
        } catch (\Exception $e) {
            \Log::error('Error al guardar límites: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

}

