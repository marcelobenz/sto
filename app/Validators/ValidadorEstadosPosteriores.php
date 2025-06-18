<?php

namespace App\Validators;

use App\Interfaces\ValidadorEstado;
use App\Builder\EstadoBuilder;
use Exception;

class ValidadorEstadosPosteriores implements ValidadorEstado
{
    public function validar(EstadoBuilder $estadoBuilder): void
    {
        if (
            $estadoBuilder->aceptaPosteriores() &&
            (empty($estadoBuilder->getEstadosPosteriores()))
        ) {
            throw new Exception("Debe asignar al menos un estado posterior al estado '{$estadoBuilder->getNombre()}'");
        }
    }
}
