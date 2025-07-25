<?php

namespace App\Validadores;

use App\Interfaces\ValidadorEstado;
use App\Builders\EstadoBuilder;
use Exception;

class ValidadorAsignables implements ValidadorEstado
{
    public function validar(EstadoBuilder $estadoBuilder): void
    {
        if (empty($estadoBuilder->getAsignables())) {
            throw new Exception("Debe asignar al menos un usuario o grupo al estado '{$estadoBuilder->getNombre()}'");
        }
    }
}