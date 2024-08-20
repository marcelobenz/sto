<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioView extends Model
{
    private String $apellido;
    private String $cargo;
	private String $discapacitado;
	private String $fechaIngreso;
	private String $fechaNacimiento;
	private String $juridiccion;
	private String $cuil;
	private String $mail;
	private String $nombre;
	private String $oficina;
	private String $user;
  
    public function __construct(String $apellido, String $cargo, String $discapacitado, String $fechaIngreso, String $fechaNacimiento, 
        String $juridiccion, String $cuil, String $mail, String $nombre, String $oficina, String $user) {
            $this->apellido = $apellido;
            $this->cargo = $cargo;
            $this->discapacitado = $discapacitado;
            $this->fechaIngreso = $fechaIngreso;
            $this->fechaNacimiento = $fechaNacimiento;
            $this->juridiccion = $juridiccion;
            $this->cuil = $cuil;
            $this->mail = $mail;
            $this->nombre = $nombre;
            $this->oficina = $oficina;
            $this->user = $user;
    }
}
