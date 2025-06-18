<?php

namespace App\Validators;

use App\Interfaces\ValidadorEstado;
use App\Builder\EstadoBuilder;
use Exception;

class ValidadorTipoEstado implements ValidadorEstado
{
    public function validar(EstadoBuilder $estadoBuilder): void
    {
        if ($estadoBuilder->getTipoEstado() === null) {
            throw new Exception("El estado '{$estadoBuilder->getNombre()}' no tiene un tipo");
        }
    }
}
