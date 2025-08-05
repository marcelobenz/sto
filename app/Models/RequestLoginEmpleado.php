<?php

namespace App\Models;

use App\Models\UsuarioView;
use Illuminate\Database\Eloquent\Model;

class RequestLoginEmpleado extends Model
{
    private String $urlRedirect;
	private String $urlResponse;
	private UsuarioView $usuarios;
	private UsuarioView $usuario;

    public function setUsuario(UsuarioView $usuario) {
		$this->usuario = $usuario;
	}

    public function getUsuario() {
		return $this->usuario;
	}
}
