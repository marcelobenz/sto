<?php

namespace App\Repositories;

use App\Models\Licencia;
use App\Models\UsuarioInterno;

class LicenciaRepository
{
    public function findUsuarioById($id)
    {
        return UsuarioInterno::findOrFail($id);
    }

    public function getHistorialLicenciasUsuario($idUsuario)
    {
        return Licencia::where('id_usuario_interno', $idUsuario)->get();
    }

    public function createLicencia(array $data)
    {
        return Licencia::create($data);
    }
}
