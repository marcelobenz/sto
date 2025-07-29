<?php

namespace App\DTOs;

class CuentaDTO
{
    private $codigo;

    private $descripcion;

    public function __construct(array $data = [])
    {
        $this->codigo = $data['codigo'] ?? null;
        $this->descripcion = $data['descripcion'] ?? null;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }
}
