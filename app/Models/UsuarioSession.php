<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Datetime;

class UsuarioSession extends Model
{
	private int $id;
    private String $legajo;
    private String $nombre;
    private String $apellido;
    private String $correo;
    private int $estado;
    private $permisos;
    private bool $flagMenu;
    private int $limite;
    private DateTime $fechaUltimoIngreso;
  
    public function ConstructorWithArgument10(int $id, String $legajo, String $nombre, String $apellido, String $correo,
        int $estado, $permisos, bool $flagMenu, int $limite, DateTime $fechaUltimoIngreso) {
            $this->id = $id;
            $this->legajo = $legajo;
            $this->nombre = $nombre;
            $this->apellido = $apellido;
            $this->correo = $correo;
            $this->estado = $estado;
            $this->permisos = $permisos;
            $this->flagMenu = $flagMenu;
            $this->limite = $limite;
            $this->fechaUltimoIngreso = $fechaUltimoIngreso->getTimestamp();
    }

    public function ConstructorWithArgument9(int $id, String $legajo, String $nombre, String $apellido, String $correo,
        int $estado, $permisos, bool $flagMenu, int $limite) {
            $this->id = $id;
            $this->legajo = $legajo;
            $this->nombre = $nombre;
            $this->apellido = $apellido;
            $this->correo = $correo;
            $this->estado = $estado;
            $this->permisos = $permisos;
            $this->flagMenu = $flagMenu;
            $this->limite = $limite;
    }

    public function __construct() { 
        $arguments = func_get_args(); 
        $numberOfArguments = func_num_args(); 
  
        if (method_exists($this, $function =  
                'ConstructorWithArgument'.$numberOfArguments)) { 
            call_user_func_array( 
                        array($this, $function), $arguments); 
        } 
    }

    public function getId() {
        return $this->id;
    }

    public function getLegajo() {
        return $this->legajo;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getPermisos() {
        return $this->permisos;
    }

    public function getFlagMenu() {
        return $this->flagMenu;
    }

    public function getLimite() {
        return $this->limite;
    }
}
