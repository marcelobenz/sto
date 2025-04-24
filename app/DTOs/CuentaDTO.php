<?php

namespace App\DTOs;

class CuentaDTO {
    private string $codigo;
    private string $descripcion;

    public function __construct(
        string $codigo,
        string $descripcion,
    ) {
        $this->codigo = $codigo;
        $this->descripcion = $descripcion;
    }

    public function getCodigo(): string {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): void {
        $this->codigo = $codigo;
    }

    public function getDescripcion(): string {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void {
        $this->descripcion = $descripcion;
    }
}
