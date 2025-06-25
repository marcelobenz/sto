<?php

namespace App\Validadores;

use App\Interfaces\ValidadorEstado;
use App\Builders\EstadoBuilder;
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
