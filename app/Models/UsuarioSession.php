<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permiso;
use \Datetime;

class UsuarioSession extends Model
{
	private int $id;
    private String $legajo;
    private String $nombre;
    private String $apellido;
    private String $correo;
    private Permiso $permisos;
    private bool $flagMenu;
    private int $limite;
    private DateTime $fechaUltimoIngreso;
  
    public function __construct(int $id, String $legajo, String $nombre, String $apellido, String $correo,
        Permiso $permisos, bool $flagMenu, int $limite) {
            $this->id = $id;
            $this->legajo = $legajo;
            $this->nombre = $nombre;
            $this->apellido = $apellido;
            $this->correo = $correo;
            $this->permisos = $permisos;
            $this->flagMenu = $flagMenu;
            $this->limite = $limite;
            $this->fechaUltimoIngreso = $fechaUltimoIngreso->getTimestamp();
    }
}
