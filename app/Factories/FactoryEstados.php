<?php

namespace App\Factories;

use App\Builders\EstadoBuilder;
use App\DTOs\EstadoTramiteDTO;
use App\Enums\TipoEstadoEnum;
use Exception;
use Illuminate\Support\Collection;

class FactoryEstados
{
    /**
     * @param  Collection<int, EstadoBuilder>  $estadosBuilder
     * @return Collection<int, EstadoTramiteDTO>
     *
     * @throws Exception
     */
    public function construirEstados(Collection $estadosBuilder): Collection
    {
        $estados = collect();

        // First, link previous states for those in creation
        foreach ($estadosBuilder as $estadoBuilder) {
            if ($estadoBuilder->getTipoEstado() === TipoEstadoEnum::EN_CREACION) {
                $estadoBuilder->vincularAnteriores();
            }
        }

        // Build EstadoTramiteDTO objects from EstadoBuilders
        foreach ($estadosBuilder as $estadoBuilder) {
            $estadoTramite = $estadoBuilder->build();
            $estados->push($estadoTramite);
            // Set ID on DTO explicitly (like Java does)
            $estadoTramite->setId($estadoBuilder->id);
        }

        // Assign previous, next, and node previous states on DTOs
        foreach ($estados as $estadoTramite) {
            $this->asignarEstadosAnteriores($estadoTramite, $estados, $estadosBuilder);
            $this->asignarEstadosPosteriores($estadoTramite, $estados, $estadosBuilder);
            $this->asignarNodosAnteriores($estadoTramite, $estados, $estadosBuilder);
        }

        return $estados;
    }

    private function asignarNodosAnteriores(
        EstadoTramiteDTO $estado,
        Collection $estados,
        Collection $estadosBuilder
    ): void {
        foreach ($estadosBuilder as $estadoBuilder) {
            if ($estadoBuilder->nombre === $estado->getNombre()) {
                $nuevos = $this->buscarNodosAnteriores($estadoBuilder, $estados);

                // Une ambas colecciones y la vuelve a guardar en el DTO
                $estado->setNodosAnteriores(
                    $estado->getNodosAnteriores()->merge($nuevos)
                );
            }
        }
    }

    /**
     * @param  Collection<int, EstadoTramiteDTO>  $estados
     * @return Collection<int, EstadoTramiteDTO>
     */
    private function buscarNodosAnteriores(
        EstadoBuilder $estadoBuilder,
        Collection $estados
    ): Collection {
        $result = collect();

        foreach ($estadoBuilder->nodosAnteriores as $estadoBuilderAux) {
            foreach ($estados as $estadoTramiteAux) {
                if ($estadoBuilderAux->nombre === $estadoTramiteAux->getNombre()) {
                    $result->push($estadoTramiteAux);
                }
            }
        }

        return $result;
    }

    private function asignarEstadosAnteriores(
        EstadoTramiteDTO $estado,
        Collection $estados,
        Collection $estadosBuilder
    ): void {
        foreach ($estadosBuilder as $estadoBuilder) {
            if ($estadoBuilder->nombre === $estado->getNombre()) {
                $anteriores = $this->buscarEstadosAnteriores($estadoBuilder, $estados);

                $estado->setEstadosAnteriores(
                    $estado->getEstadosAnteriores()->merge($anteriores)
                );
            }
        }
    }

    private function asignarEstadosPosteriores(
        EstadoTramiteDTO $estado,
        Collection $estados,
        Collection $estadosBuilder
    ): void {
        foreach ($estadosBuilder as $estadoBuilder) {
            if ($estadoBuilder->nombre === $estado->getNombre()) {
                $posteriores = $this->buscarEstadosPosteriores($estadoBuilder, $estados);

                $estado->setEstadosPosteriores(
                    $estado->getEstadosPosteriores()->merge($posteriores)
                );
            }
        }
    }

    /**
     * @param  Collection<int, EstadoTramiteDTO>  $estados
     * @return Collection<int, EstadoTramiteDTO>
     */
    private function buscarEstadosAnteriores(
        EstadoBuilder $estadoBuilder,
        Collection $estados
    ): Collection {
        $result = collect();

        foreach ($estadoBuilder->estadosAnteriores as $estadoBuilderAux) {
            foreach ($estados as $estadoTramiteAux) {
                if ($estadoBuilderAux->nombre === $estadoTramiteAux->getNombre()) {
                    $result->push($estadoTramiteAux);
                }
            }
        }

        return $result;
    }

    /**
     * @param  Collection<int, EstadoTramiteDTO>  $estados
     * @return Collection<int, EstadoTramiteDTO>
     */
    private function buscarEstadosPosteriores(
        EstadoBuilder $estadoBuilder,
        Collection $estados
    ): Collection {
        $result = collect();

        foreach ($estadoBuilder->estadosPosteriores as $estadoBuilderAux) {
            foreach ($estados as $estadoTramiteAux) {
                if ($estadoBuilderAux->nombre === $estadoTramiteAux->getNombre()) {
                    $result->push($estadoTramiteAux);
                }
            }
        }

        return $result;
    }
}
