<?php

namespace App\Validadores;

use App\Builders\EstadoBuilder;
use App\Interfaces\ValidadorEstado;
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
