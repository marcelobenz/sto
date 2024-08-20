<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingresante extends Model
{
	private String $cuil;
  
    public function __construct(String $cuil) {
        $this->cuil = $cuil;
    }

    /* public void ingresar() throws ExcepcionControladaError {
        UsuarioSession usuarioSession = new UsuarioInternoController().recuperarPorCuilUSesion(this.cuil);
        Integer activo = new UsuarioInternoController().recuperarPorCuilActivo(this.cuil);
        if (usuarioSession == null) {
            throw new RuntimeAlert("El usuario ingresado es incorrecto");
        }
        if (activo != 1){
            throw new RuntimeAlert("El usuario ingresado no esta activo");
        }
        this.mantenerUsuarioEnSession(usuarioSession);
    }

    private void mantenerUsuarioEnSession(UsuarioSession usuarioSession) {
        UsuarioSession usuarioSessionLogueado = ((UsuarioSession) new SessionController().getLoggedUser());
        if (usuarioSessionLogueado == null) {
            usuarioSessionLogueado = usuarioSession;
        }
        usuarioSessionLogueado.actualizarUltimoRequest();
        new SessionController().keepOnSession(usuarioSession);
    } */
}
