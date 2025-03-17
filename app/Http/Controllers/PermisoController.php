<?php

namespace App\Http\Controllers;

use App\Models\UsuarioPermiso;
use App\Models\PermisoEnum;

class PermisoController extends Controller
{
    public static function getPermisosPorId($id) {
        $permisos = Permiso::getPermisosPorId($id);
        $permisosEnum = array();
        foreach ($permisos as &$p) {
            array_push($permisosEnum, PermisoEnum::from($p->permiso));
        }
        return $permisos;
    }
}
