<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLoginEmpleado extends Model
{
    private string $urlRedirect;

    private string $urlResponse;

    private UsuarioView $usuarios;

    private UsuarioView $usuario;

    public function setUsuario(UsuarioView $usuario)
    {
        $this->usuario = $usuario;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }
}
