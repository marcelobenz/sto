<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class PermisosHelper
{
    public static function getPermisos($idUsuario, array $permisos)
    {
        return DB::table('usuario_permiso')
            ->where('id_usuario_interno', $idUsuario)
            ->whereIn('permiso', $permisos)
            ->pluck('permiso')
            ->toArray();
    }

    public static function tienePermiso($idUsuario, $permiso)
    {
        return DB::table('usuario_permiso')
            ->where('id_usuario_interno', $idUsuario)
            ->where('permiso', $permiso)
            ->exists();
    }
}
