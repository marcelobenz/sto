<?php

namespace App\Repositories;

use App\Models\Rol;
use App\Models\Permiso;

class RolRepository
{
    public function getAllRoles()
    {
        return Rol::all();
    }

    public function findRoleById($id)
    {
        return Rol::findOrFail($id);
    }

    public function getAllPermisos()
    {
        return Permiso::all();
    }

    public function getPermisosIdsByRol(Rol $rol)
    {
        return $rol->permisos->pluck('id_permiso')->toArray();
    }

    public function saveRol(Rol $rol)
    {
        $rol->save();
        return $rol;
    }

    public function syncPermisos(Rol $rol, array $permisos)
    {
        $rol->permisos()->sync($permisos);
    }

    public function createRol(array $data)
    {
        return Rol::create($data);
    }
}
