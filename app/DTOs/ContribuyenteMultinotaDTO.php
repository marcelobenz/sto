<?php

namespace App\DTOs;

use DateTime;

class ContribuyenteMultinotaDTO
{
    private int $idContribuyenteMultinota;
    private string $cuit;
    private string $nombre;
    private string $apellido;
    private string $correo;
    private ?string $telefono1;
    private ?string $telefono2;
    private string $clave;
    private int $idDireccion;
    private int $activo;
    private int $cantidadIntentos;
    private ?string $codigoActivacion;
    private DateTime $fechaActivacion;

    public function __construct(
        int $idContribuyenteMultinota,
        string $cuit,
        string $nombre,
        string $apellido,
        string $correo,
        ?string $telefono1,
        ?string $telefono2,
        string $clave,
        int $idDireccion,
        int $activo,
        int $cantidadIntentos,
        ?string $codigoActivacion,
        DateTime $fechaActivacion
    ) {
        $this->idContribuyenteMultinota = $idContribuyenteMultinota;
        $this->cuit = $cuit;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->correo = $correo;
        $this->telefono1 = $telefono1;
        $this->telefono2 = $telefono2;
        $this->clave = $clave;
        $this->idDireccion = $idDireccion;
        $this->activo = $activo;
        $this->cantidadIntentos = $cantidadIntentos;
        $this->codigoActivacion = $codigoActivacion;
        $this->fechaActivacion = $fechaActivacion;
    }

    public function getIdContribuyenteMultinota(): int {
        return $this->idContribuyenteMultinota;
    }

    public function setIdContribuyenteMultinota(int $id): void {
        $this->idContribuyenteMultinota = $id;
    }

    public function getCuit(): string {
        return $this->cuit;
    }

    public function setCuit(string $cuit): void {
        $this->cuit = $cuit;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function getApellido(): string {
        return $this->apellido;
    }

    public function setApellido(string $apellido): void {
        $this->apellido = $apellido;
    }

    public function getCorreo(): string {
        return $this->correo;
    }

    public function setCorreo(string $correo): void {
        $this->correo = $correo;
    }

    public function getTelefono1(): ?string {
        return $this->telefono1;
    }

    public function setTelefono1(?string $telefono1): void {
        $this->telefono1 = $telefono1;
    }

    public function getTelefono2(): ?string {
        return $this->telefono2;
    }

    public function setTelefono2(?string $telefono2): void {
        $this->telefono2 = $telefono2;
    }

    public function getClave(): string {
        return $this->clave;
    }

    public function setClave(string $clave): void {
        $this->clave = $clave;
    }

    public function getIdDireccion(): int {
        return $this->idDireccion;
    }

    public function setIdDireccion(int $idDireccion): void {
        $this->idDireccion = $idDireccion;
    }

    public function getActivo(): int {
        return $this->activo;
    }

    public function setActivo(int $activo): void {
        $this->activo = $activo;
    }

    public function getCantidadIntentos(): int {
        return $this->cantidadIntentos;
    }

    public function setCantidadIntentos(int $cantidadIntentos): void {
        $this->cantidadIntentos = $cantidadIntentos;
    }

    public function getCodigoActivacion(): ?string {
        return $this->codigoActivacion;
    }

    public function setCodigoActivacion(?string $codigoActivacion): void {
        $this->codigoActivacion = $codigoActivacion;
    }

    public function getFechaActivacion(): DateTime {
        return $this->fechaActivacion;
    }

    public function setFechaActivacion(DateTime $fechaActivacion): void {
        $this->fechaActivacion = $fechaActivacion;
    }
}
