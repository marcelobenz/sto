<?php

namespace App\Transformers;

use App\DTOs\PersonaJuridicaDTO;

class PersonaJuridicaTransformer
{
    protected PersonaJuridicaDTO $persona;

    protected array $cuentas;

    public function personaJuridica(PersonaJuridicaDTO $persona): self
    {
        $this->persona = $persona;

        return $this;
    }

    public function cuentas(array $cuentas): self
    {
        $this->cuentas = $cuentas;

        return $this;
    }

    public function transform(): array
    {
        return [
            'persona' => $this->persona,
            'cuentas' => $this->cuentas,
        ];
    }
}
