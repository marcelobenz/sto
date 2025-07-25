<?php

namespace App\Http\Controllers;

use App\Models\UsuarioInterno;
use App\Models\UsuarioSession;

class UsuarioInternoController extends Controller
{
    public static function getUsuarioSessionPorCuit(String $cuit) {
        try {
            $usuarioInterno = UsuarioInterno::with('rol.permisos')->where('cuit', $cuit)->first();;
            $permisos = $usuarioInterno->permisos();
            return new UsuarioSession($usuarioInterno->id_usuario_interno, $usuarioInterno->legajo, $usuarioInterno->nombre, $usuarioInterno->apellido, 
                $usuarioInterno->correo === null ? $usuarioInterno->correo_municipal : $usuarioInterno->correo,
                $usuarioInterno->estado, $permisos, $usuarioInterno->flag_menu, $usuarioInterno->limite === null ? 0 : $usuarioInterno->limite);
        } catch (\Throwable $th) {
            throw new Exception($th);
        }
    }
}
