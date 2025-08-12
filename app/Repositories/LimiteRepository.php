<?php

namespace App\Repositories;

use App\Models\GrupoInterno;
use App\Models\UsuarioInterno;
use Illuminate\Support\Facades\Log;

class LimiteRepository
{
    public function obtenerGruposConUsuarios()
    {
        return GrupoInterno::with('usuarios')->get();
    }

    public function actualizarLimiteUsuario(int $idUsuario, float $limite): bool
    {
        try {
            $usuario = UsuarioInterno::find($idUsuario);
            
            if (!$usuario) {
                Log::error('Usuario no encontrado', ['id' => $idUsuario]);
                return false;
            }

            $usuario->limite = $limite;
            return $usuario->save();
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar límite: ' . $e->getMessage(), [
                'id_usuario' => $idUsuario,
                'limite' => $limite
            ]);
            return false;
        }
    }
}