<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\UsuarioInternoController;
use \stdClass;

class Ingresante extends Model
{
	private String $cuil;
  
    public function __construct(String $cuil) {
        $this->cuil = $cuil;
    }

    public function ingresar() {
        $usuarioSession = UsuarioInternoController::getUsuarioSessionPorCuil($this->cuil);

        if ($usuarioSession === null) {
            throw new Exception("El usuario ingresado es incorrecto");
        }

        if ($usuarioSession->getEstado() !== 1){
            throw new Exception("El usuario ingresado no esta activo");
        }

        $object = new stdClass();

        $object->id = $usuarioSession->getId();
        $object->legajo = $usuarioSession->getLegajo();
        $object->nombre = $usuarioSession->getNombre();
        $object->apellido = $usuarioSession->getApellido();
        $object->correo = $usuarioSession->getCorreo();
        $object->estado = $usuarioSession->getEstado();
        $object->permisos = $usuarioSession->getPermisos();
        $object->flagMenu = $usuarioSession->getFlagMenu();
        $object->limite = $usuarioSession->getLimite();

        Session::put('USUARIO', $object);

        return;
    }
}
