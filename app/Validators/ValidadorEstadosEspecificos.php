<?php

namespace App\Validators;

use App\Interfaces\ValidadorEstado;
use App\Builder\EstadoBuilder;
use Exception;

class ValidadorEstadosEspecificos implements ValidadorEstado
{
    private EstadoBuilder $estadoEspecifico;

    public function validar(EstadoBuilder $estadoBuilder): void
    {
        $this->estadoEspecifico = $estadoBuilder;
        $this->buscarBifurcacionesEnEstadosAnteriores($estadoBuilder);
    }

    private function buscarBifurcacionesEnEstadosAnteriores(EstadoBuilder $estado): void
    {
        foreach ($estado->getNodosAnteriores() as $estadoAnterior) {
            if ($estadoAnterior->isPuedeElegirCamino() && !$this->estadoEspecifico->esObligatorio()) {
                throw new Exception("El estado {$this->estadoEspecifico->getNombre()} debe estar dentro de un flujo sin bifurcaciones");
            } else {
                $this->buscarBifurcacionesEnEstadosAnteriores($estadoAnterior);
            }
        }
    }
}