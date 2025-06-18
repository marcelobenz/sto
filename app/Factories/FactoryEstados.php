<?php

namespace App\Factories;

use Exception;
use Illuminate\Support\Collection;
use App\Builders\EstadoBuilder;
use App\DTOs\EstadoTramiteDTO; 

class FactoryEstados {
  /**
   * @param Collection<int, EstadoBuilder> $estadosBuilder
   * @return Collection<int, EstadoTramiteDTO>
   * @throws Exception
   */
  public function construirEstados(Collection $estadosBuilder): Collection {
    $estados = collect();

    // First, link previous states for those in creation
    foreach ($estadosBuilder as $estadoBuilder) {
      if ($estadoBuilder->tipoEstado->equals(TipoEstadoEnum::EN_CREACION)) {
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
        $estado->getNodosAnteriores()->add(
          ...$this->buscarNodosAnteriores($estadoBuilder, $estados)
        );
      }
    }
  }

  /**
   * @param EstadoBuilder $estadoBuilder
   * @param Collection<int, EstadoTramiteDTO> $estados
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
        $estado->getEstadosAnteriores()->add(
          ...$this->buscarEstadosAnteriores($estadoBuilder, $estados)
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
        $estado->getEstadosPosteriores()->add(
          ...$this->buscarEstadosPosteriores($estadoBuilder, $estados)
        );
      }
    }
  }

  /**
   * @param EstadoBuilder $estadoBuilder
   * @param Collection<int, EstadoTramiteDTO> $estados
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
   * @param EstadoBuilder $estadoBuilder
   * @param Collection<int, EstadoTramiteDTO> $estados
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
