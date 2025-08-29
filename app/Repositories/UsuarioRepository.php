<?php

namespace App\Repositories;

use App\Models\UsuarioInterno;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;

class UsuarioRepository
{
    public function getAllUsuarios()
    {
        return DB::table('usuario_interno as ui')
            ->join('grupo_interno as gi', 'ui.id_grupo_interno', '=', 'gi.id_grupo_interno')
            ->join('oficina as o', 'gi.id_oficina', '=', 'o.id_oficina')
            ->select(
                'ui.id_usuario_interno',
                'ui.legajo',
                'ui.nombre',
                'ui.apellido',
                'ui.correo_municipal',
                'gi.descripcion as grupo_descripcion',
                'o.descripcion as oficina_descripcion'
            )
            ->get();
    }

    public function getRoles()
    {
        return Rol::all();
    }

    public function createUsuario(array $data)
    {
        return UsuarioInterno::create($data);
    }

    public function findUsuario($id)
    {
        return UsuarioInterno::findOrFail($id);
    }

    public function updateUsuario($id, array $data)
    {
        $usuario = $this->findUsuario($id);
        $usuario->update($data);
        return $usuario;
    }

    public function getPermisosPorRol($idRol)
    {
        return Rol::with('permisos')->find($idRol);
    }

    public function getUsuarioByLegajo($legajo)
    {
        return UsuarioInterno::with('rol.permisos')
            ->where('legajo', $legajo)
            ->first();
    }
}
