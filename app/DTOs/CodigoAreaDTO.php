<?php

namespace App\DTOs;

class CodigoAreaDTO {
    private int $id;
    private string $provincia;
    private string $localidad;
    private string $codigo;

    public function __construct(
        int $id,
        string $provincia,
        string $localidad,
        string $codigo,
    ) {
        $this->id = $id;
        $this->provincia = $provincia;
        $this->localidad = $localidad;
        $this->codigo = $codigo;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getProvincia(): string {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): void {
        $this->provincia = $provincia;
    }

    public function getLocalidad(): string {
        return $this->localidad;
    }

    public function setLocalidad(string $localidad): void {
        $this->localidad = $localidad;
    }

    public function getCodigo(): string {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): void {
        $this->codigo = $codigo;
    }
}
