<?php

namespace App\DTOs;

class DomicilioDTO {
    private int $id;
    private string $pais;
    private string $provincia;
    private string $partido;
    private string $localidad;
    private string $latitud;
    private string $longitud;
    private string $calle;
    private string $numero;
    private string $piso;
    private string $departamento;
    private string $codigoPostal;

    public function __construct(
        string $calle,
        string $numero,
        string $localidad,
        string $provincia,
        string $codigoPostal,
        string $pais,
        string $latitud,
        string $longitud,
        string $piso,
        string $departamento
    ) {
        $this->calle = $calle;
        $this->numero = $numero;
        $this->localidad = $localidad;
        $this->provincia = $provincia;
        $this->codigoPostal = $codigoPostal;
        $this->pais = $pais;
        $this->latitud = $latitud;
        $this->longitud = $longitud;
        $this->piso = $piso;
        $this->departamento = $departamento;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getPais(): string {
        return $this->pais;
    }

    public function setPais(string $pais): void {
        $this->pais = $pais;
    }
    
    public function getProvincia(): string {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): void {
        $this->provincia = $provincia;
    }

    public function getPartido(): string {
        return $this->partido;
    }

    public function setPartido(string $partido): void {
        $this->partido = $partido;
    }

    public function getLocalidad(): string {
        return $this->localidad;
    }

    public function setLocalidad(string $localidad): void {
        $this->localidad = $localidad;
    }

    public function getLatitud(): string {
        return $this->latitud;
    }

    public function setLatitud(string $latitud): void {
        $this->latitud = $latitud;
    }

    public function getLongitud(): string {
        return $this->longitud;
    }

    public function setLongitud(string $longitud): void {
        $this->longitud = $longitud;
    }

    public function getCalle(): string {
        return $this->calle;
    }

    public function setCalle(string $calle): void {
        $this->calle = $calle;
    }

    public function getNumero(): string {
        return $this->numero;
    }

    public function setNumero(string $numero): void {
        $this->numero = $numero;
    }

    public function getPiso(): string {
        return $this->piso;
    }

    public function setPiso(string $piso): void {
        $this->piso = $piso;
    }

    public function getDepartamento(): string {
        return $this->departamento;
    }

    public function setDepartamento(string $departamento): void {
        $this->departamento = $departamento;
    }

    public function getCodigoPostal(): string {
        return $this->codigoPostal;
    }

    public function setCodigoPostal(string $codigoPostal): void {
        $this->codigoPostal = $codigoPostal;
    }
}