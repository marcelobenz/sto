<?php

namespace App\DTOs;

class PersonaFisicaDTO
{
    private string $cuit;

    private string $dni;

    private string $nombre;

    private string $apellido;

    private string $nacionalidad;

    private string $fechaNacimiento;

    private string $estadoCivil;

    private string $sexo;

    private string $correo;

    private string $telefono;

    private string $celular;

    private string $direccion;

    public function __construct(
        private bool $debeCargarRepresentante = false,
        private bool $debePersistirseConTramite = false,
        private bool $edicionBloqueada = false,
        private bool $puedeIniciarTramite = false
    ) {}

    public function getTitular(): string
    {
        return $this->apellido.', '.$this->nombre;
    }

    public function getCuit(): string
    {
        return $this->cuit;
    }

    public function setCuit(string $cuit): void
    {
        $this->cuit = $cuit;
    }

    public function getDni(): string
    {
        return $this->dni;
    }

    public function setDni(string $dni): void
    {
        $this->dni = $dni;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getApellido(): string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): void
    {
        $this->apellido = $apellido;
    }

    public function getNacionalidad(): string
    {
        return $this->nacionalidad;
    }

    public function setNacionalidad(string $nacionalidad): void
    {
        $this->nacionalidad = $nacionalidad;
    }

    public function getFechaNacimiento(): string
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(string $fechaNacimiento): void
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    public function getEstadoCivil(): string
    {
        return $this->estadoCivil;
    }

    public function setEstadoCivil(string $estadoCivil): void
    {
        $this->estadoCivil = $estadoCivil;
    }

    public function getSexo(): string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): void
    {
        $this->sexo = $sexo;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public function getCelular(): string
    {
        return $this->celular;
    }

    public function setCelular(string $celular): void
    {
        $this->celular = $celular;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): void
    {
        $this->direccion = $direccion;
    }

    public function getDebeCargarRepresentante(): bool
    {
        return $this->debeCargarRepresentante;
    }

    public function getDebePersistirseConTramite(): bool
    {
        return $this->debePersistirseConTramite;
    }

    public function getEdicionBloqueada(): bool
    {
        return $this->edicionBloqueada;
    }

    public function getPuedeIniciarTramite(): bool
    {
        return $this->puedeIniciarTramite;
    }
}
