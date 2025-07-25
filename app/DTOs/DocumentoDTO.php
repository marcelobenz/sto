<?php

namespace App\DTOs;

class DocumentoDTO {
    private string $tipo;
    private string $numero;

    public function __construct(
        string $numero,
        string $tipo = 'CUIT'
    ) {
        $this->numero = $numero;
        $this->tipo = $tipo;
    }

    public function getTipo(): string {
        return $this->tipo;
    }

    public function setTipo(string $tipo): void {
        $this->tipo = $tipo;
    }

    public function getNumero(): string {
        return $this->numero;
    }

    public function setNumero(string $numero): void {
        $this->numero = $numero;
    }
}
