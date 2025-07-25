<?php

namespace App\Validadores;

use App\Interfaces\ValidadorEstado;
use App\Builders\EstadoBuilder;
use Exception;

class ValidadorEstadosAnteriores implements ValidadorEstado
{
    public function validar(EstadoBuilder $estadoBuilder): void
    {
        if (
            $estadoBuilder->aceptaAnteriores() &&
            (empty($estadoBuilder->getEstadosAnteriores()))
        ) {
            throw new Exception("Debe asignar al menos un estado anterior al estado '{$estadoBuilder->getNombre()}'");
        }
    }
}