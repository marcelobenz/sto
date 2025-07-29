<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioView extends Model
{
    private string $apellido;

    private string $cargo;

    private string $discapacitado;

    private string $fechaIngreso;

    private string $fechaNacimiento;

    private string $juridiccion;

    private string $cuit;

    private string $mail;

    private string $nombre;

    private string $oficina;

    private string $user;

    public function __construct(
        string $apellido,
        string $cargo,
        string $discapacitado,
        string $fechaIngreso,
        string $fechaNacimiento,
        string $juridiccion,
        string $cuit,
        string $mail,
        string $nombre,
        string $oficina,
        string $user
    ) {
        $this->apellido = $apellido;
        $this->cargo = $cargo;
        $this->discapacitado = $discapacitado;
        $this->fechaIngreso = $fechaIngreso;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->juridiccion = $juridiccion;
        $this->cuit = $cuit;
        $this->mail = $mail;
        $this->nombre = $nombre;
        $this->oficina = $oficina;
        $this->user = $user;
    }

    public function getCuit()
    {
        return $this->cuit;
    }
}
