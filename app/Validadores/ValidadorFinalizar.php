<?php

namespace App\Validadores;

use App\Interfaces\ValidadorEstado;
use App\Builders\EstadoBuilder;
use Exception;

class ValidadorFinalizar implements ValidadorEstado
{
    public function validar(EstadoBuilder $estadoBuilder): void
    {
        if ($estadoBuilder->getPuedeElegirCamino()) {
            return;
        }

        foreach ($estadoBuilder->getEstadosPosteriores() as $estado) {
            if (
                $estado->getTipoEstado() === 'A_FINALIZAR' &&
                count($estadoBuilder->getEstadosPosteriores()) > 1
            ) {
                throw new Exception("El estado '{$estadoBuilder->getNombre()}' no puede tener más estados posteriores además del estado '{$estado->getNombre()}'");
            }
        }
    }
}