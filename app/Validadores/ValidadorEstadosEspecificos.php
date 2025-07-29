<?php

namespace App\Validadores;

use App\Builders\EstadoBuilder;
use App\Interfaces\ValidadorEstado;
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
            if ($estadoAnterior->getPuedeElegirCamino() && ! $this->estadoEspecifico->esObligatorio()) {
                throw new Exception("El estado {$this->estadoEspecifico->getNombre()} debe estar dentro de un flujo sin bifurcaciones");
            } else {
                $this->buscarBifurcacionesEnEstadosAnteriores($estadoAnterior);
            }
        }
    }
}
