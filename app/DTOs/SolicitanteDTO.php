<?php

namespace App\DTOs;

class SolicitanteDTO
{
    public string $cuenta = '';

    public string $correo = '';

    public string $cuit = '';

    public string $apellido = '';

    public string $direccion = '';

    public function getCuenta(): string
    {
        return $this->cuenta;
    }

    public function setCuenta(?string $cuenta): void
    {
        $this->cuenta = $cuenta ?? '';
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function setCorreo(?string $correo): void
    {
        $this->correo = $correo ?? '';
    }

    public function getCuit(): string
    {
        return $this->cuit;
    }

    public function setCuit(?string $cuit): void
    {
        $this->cuit = $cuit ?? '';
    }

    public function getApellido(): string
    {
        return $this->apellido;
    }

    public function setApellido(?string $apellido): void
    {
        $this->apellido = $apellido ?? '';
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): void
    {
        $this->direccion = $direccion ?? '';
    }
}
