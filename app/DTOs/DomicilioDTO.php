<?php

namespace App\DTOs;

class DomicilioDTO {
    private string $provincia;
    private string $partido;
    private string $localidad;
    private string $calle;
    private string $numero;
    private string $piso;
    private string $departamento;
    private string $codigoPostal;
    
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