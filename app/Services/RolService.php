<?php

namespace App\Services;

use App\Repositories\RolRepository;
use App\Models\Rol;

class RolService
{
    protected $rolRepository;

    public function __construct(RolRepository $rolRepository)
    {
        $this->rolRepository = $rolRepository;
    }

    public function listarRoles()
    {
        return $this->rolRepository->getAllRoles();
    }

    public function obtenerDatosEdicion($id)
    {
        $rol = $this->rolRepository->findRoleById($id);
        $permisos = $this->rolRepository->getAllPermisos();
        $permisosAsignados = $this->rolRepository->getPermisosIdsByRol($rol);

        return compact('rol', 'permisos', 'permisosAsignados');
    }

    public function actualizarRol($id, array $data)
    {
        $rol = $this->rolRepository->findRoleById($id);
        $rol->nombre = $data['nombre'];
        $this->rolRepository->saveRol($rol);

        $this->rolRepository->syncPermisos($rol, $data['permisos'] ?? []);
    }

    public function obtenerDatosCreacion()
    {
        return $this->rolRepository->getAllPermisos();
    }

    public function crearRol(array $data)
    {
        $rol = new Rol();
        $rol->nombre = $data['nombre'];
        $rol->clave = strtolower(str_replace(' ', '_', trim($rol->nombre)));
        $this->rolRepository->saveRol($rol);

        $this->rolRepository->syncPermisos($rol, $data['permisos'] ?? []);
    }
}
