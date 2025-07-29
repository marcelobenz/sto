<?php

namespace App\Validadores;

use App\Builders\EstadoBuilder;
use App\Interfaces\ValidadorEstado;
use Exception;

class ValidadorBifurcaciones implements ValidadorEstado
{
    public function validar(EstadoBuilder $estadoBuilder): void
    {
        $tieneBifurcacionesPrevias = false;

        foreach ($estadoBuilder->getEstadosAnteriores() as $estado) {
            if (
                $estado->getPuedeElegirCamino() &&
                (! $estadoBuilder->esEstadoPosterior($estado) && ! $estadoBuilder->esPosteriorObligatorio($estado))
            ) {
                $tieneBifurcacionesPrevias = true;
            }
        }

        if ($tieneBifurcacionesPrevias && count($estadoBuilder->obtenerEstadosAnterioresInalcanzables()) > 1) {
            throw new Exception("El estado {$estadoBuilder->getNombre()} no puede ser elegido en un camino alternativo y lineal, ni en varios alternativos a la vez");
        }
    }
}
