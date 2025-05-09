<?php

namespace App\DTOs;

class InstanciaMultinotaDTO {
    public string $cuenta = '';
    public string $correo = '';

    public function getCuenta(): string{
        return $this->cuenta;
    }

    public function setCuenta(?string $cuenta): void{
        $this->cuenta = $cuenta ?? '';
    }

    public function getCorreo(): string{
        return $this->correo;
    }

    public function setCorreo(?string $correo): void {
        $this->correo = $correo ?? '';
    }
}
