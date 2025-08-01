<?php

namespace App\Transformers;

use App\DTOs\PersonaFisicaDTO;

class PersonaFisicaTransformer{
    protected PersonaFisicaDTO $persona;
    protected array $cuentas;

    public function personaFisica(PersonaFisicaDTO $persona): self {
        $this->persona = $persona;
        return $this;
    }

    public function cuentas(array $cuentas): self {
        $this->cuentas = $cuentas;
        return $this;
    }

    public function transform(): array {
        return [
            'persona' => $this->persona,
            'cuentas' => $this->cuentas,
        ];
    }
}