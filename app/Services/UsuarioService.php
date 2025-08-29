<?php

namespace App\Services;

use App\Repositories\UsuarioRepository;

class UsuarioService
{
    protected $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function listarUsuarios()
    {
        return $this->usuarioRepository->getAllUsuarios();
    }

    public function obtenerRoles()
    {
        return $this->usuarioRepository->getRoles();
    }

    public function crearUsuario(array $data)
    {
        // Valores por defecto
        $data['id_categoria_usuario'] = 23;
        $data['id_grupo_interno'] = 2;
        $data['estado'] = 1;

        return $this->usuarioRepository->createUsuario($data);
    }

    public function obtenerUsuario($id)
    {
        return $this->usuarioRepository->findUsuario($id);
    }

    public function actualizarUsuario($id, array $data)
    {
        return $this->usuarioRepository->updateUsuario($id, $data);
    }

    public function permisosPorRol($idRol)
    {
        return $this->usuarioRepository->getPermisosPorRol($idRol);
    }

    public function obtenerUsuarioPorLegajo($legajo)
    {
        return $this->usuarioRepository->getUsuarioByLegajo($legajo);
    }
}
