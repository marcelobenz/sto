<?php

namespace App\DTOs;

class TipoCaracterDTO {
    private int $codigo;
    private string $nombre;

    public function __construct(
        int $codigo,
        string $nombre
    ) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
    }

    public function getCodigo(): int {
        return $this->codigo;
    }

    public function setCodigo(int $codigo): void {
        $this->codigo = $codigo;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }
}
