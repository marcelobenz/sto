<?php

use App\Enums\TipoEstadoEnum;
use App\Models\EstadoTramite;
use App\Models\UsuarioInterno;
use App\Models\DTOConfiguracionEstadoTramite;
use App\Repositories\ConfiguracionEstadoTramiteDTORepository; //TO-DO
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EstadoBuilder {
  public ?int $id = null;
  public string $nombre;
  public TipoEstadoEnum $tipoEstado;
  public bool $puedePedirDocumentacion = false;
  public bool $puedeRechazar = false;
  public bool $puedeElegirCamino = false;
  public bool $tieneExpediente = false;

  /** @var Collection<int, EstadoBuilder> */
  public Collection $estadosAnteriores;
  /** @var Collection<int, EstadoBuilder> */
  public Collection $estadosPosteriores;
  /** @var Collection<int, AsignableATramite> */
  public Collection $asignables;
  /** @var Collection<int, ValidadorEstado> */
  public Collection $validadores;

  public mixed $treeResponsables = null;
  public array $treeResponsablesElegidos = [];
  public ?UsuarioInterno $usuarioInterno = null;

  /** @var Collection<int, EstadoBuilder> */
  public Collection $nodosAnteriores;

  public function __construct(string $nombre) {
    $this->nombre = $nombre;
    $this->estadosAnteriores = collect();
    $this->estadosPosteriores = collect();
    $this->asignables = collect();
    $this->validadores = collect();
    $this->nodosAnteriores = collect();
  }

  public function getAsString(): string {
    return $this->nombre;
  }

  public function validar(): void {
    foreach ($this->validadores as $validador) {
      $validador->validar($this);
    }
  }

  public function build(): EstadoTramite {
    $this->validar();

    $dtos = $this->id
      ? ConfiguracionEstadoTramiteDTORepository::find($this, false)
      : collect();

    $dto = $dtos->first();

    return new EstadoTramite(
      $this->id,
      $this->nombre,
      $this->tipoEstado,
      $this->puedePedirDocumentacion,
      $this->puedeRechazar,
      $this->puedeElegirCamino,
      $this->tieneExpediente,
      collect(),
      collect(),
      $this->asignables,
      $dto?->id_estado_tramite_anterior
    );
  }

  public function setUsuarioInterno(UsuarioInterno $usuarioInterno): self {
    $this->usuarioInterno = $usuarioInterno;
    return $this;
  }

  public function puedeElegirUnCamino(): bool {
    $puede = $this->tipoEstado->puedeElegirCamino() && $this->estadosPosteriores->count() > 1;
    if (! $puede) {
      $this->puedeElegirCamino = false;
    }
    return $puede;
  }

  public function puedeAplicarRestricciones(): bool {
    return $this->tipoEstado->puedeAplicarRestricciones();
  }

  public function agregarEstadoAnterior(EstadoBuilder $estado): void {
    $this->estadosAnteriores->push($estado);
  }

  public function agregarEstadoPosterior(EstadoBuilder $estado): void {
    $this->estadosPosteriores->push($estado);
  }

  public function agregarAsignable(AsignableATramite $asignable): void {
    $this->asignables->push($asignable);
  }

  public function agregarValidador(ValidadorEstado $validador): void {
    $this->validadores->push($validador);
  }

  public function esFijo(): bool {
    return $this->tipoEstado !== TipoEstadoEnum::PERSONALIZADO;
  }

  public function aceptaAnteriores(): bool {
    return $this->tipoEstado->aceptaAnteriores();
  }

  public function aceptaPosteriores(): bool{
    return $this->tipoEstado->aceptaPosteriores();
  }

  public function agregarUsuarioATreeNode($nodo): void {
    $lista = collect($this->treeResponsablesElegidos);

    foreach ([$nodo, $nodo->getParent(), $nodo->getParent()?->getParent()] as $candidate) {
      if ($candidate && !$lista->contains($candidate)) {
        $lista->push($candidate);
      }
    }

    $this->treeResponsablesElegidos = $lista->all();
  }

  public function vincularAnteriores(?EstadoBuilder $anterior = null, ?Collection $analizados = null): void {
    $analizados ??= collect();

    if ($analizados->contains($this) && $anterior?->puedeElegirCamino) {
      return;
    }

    if (!$analizados->contains($this)) {
      $analizados->push($this);
    }

    if ($anterior) {
      if (!$this->nodosAnteriores->contains($anterior)) {
        $this->nodosAnteriores->push($anterior);
      }

      $alternativos = $this->estadosAnteriores->filter(
        fn($estado) => $estado->puedeElegirCamino
      )->count();

      if ($anterior->puedeElegirCamino && $this->nodosAnteriores->isNotEmpty() && $alternativos > 1 && $this->tipoEstado !== TipoEstadoEnum::A_FINALIZAR) {
        $this->nodosAnteriores = $this->nodosAnteriores->reject(fn($e) => $e === $anterior);
      }
    }

    foreach ($this->estadosPosteriores as $posterior) {
      $posterior->vincularAnteriores($this, $analizados);
    }
  }

  public function isNodoAnterior(EstadoBuilder $alQueQuieroIr): bool {
    if ($this->nodosAnteriores->contains($alQueQuieroIr)) {
      return true;
    }

    foreach ($this->nodosAnteriores as $anterior) {
      if ($anterior->isNodoAnterior($alQueQuieroIr)) {
        return true;
      }
    }

    return false;
  }

  public function obtenerEstadosAnterioresInalcanzables(): Collection {
    return $this->estadosAnteriores->reject(fn($e) => $this->esEstadoPosterior($e));
  }

  public function esPosteriorObligatorio(EstadoBuilder $estado): bool {
    if (!$this->esObligatorio()) return false;

    foreach ($estado->estadosPosteriores as $posterior) {
      if (!$this->equals($posterior) && !$posterior->esEstadoPosterior($this)) {
        return false;
      }
    }
    return true;
  }

  public function esEstadoPosterior(EstadoBuilder $posiblePosterior): bool {
    foreach ($this->estadosPosteriores as $estado) {
      if (!$this->isNodoAnterior($estado)) {
        if ($estado === $posiblePosterior) {
          return true;
        }
        if ($estado->esEstadoPosterior($posiblePosterior)) {
          return true;
        }
      }
    }
    return false;
  }

  public function esObligatorio(): bool {
    foreach ($this->obtenerEstadosAnterioresRecursivo() as $anterior) {
      if ($anterior->puedeElegirCamino) {
        foreach ($anterior->estadosPosteriores as $posterior) {
          if ($posterior !== $this && !$this->esEstadoPosterior($posterior)) {
            return false;
          }
        }
      }
    }
    return true;
  }

  private function obtenerEstadosAnterioresRecursivo(?EstadoBuilder $context = null, ?Collection $result = null): Collection {
    $context ??= $this;
    $result ??= collect();

    foreach ($context->estadosAnteriores as $estado) {
      if ($context->isNodoAnterior($estado)) {
        $result->push($estado);
        $this->obtenerEstadosAnterioresRecursivo($estado, $result);
      }
    }

    return $result;
  }

  public function equals(mixed $other): bool {
    return $other instanceof self && $this->id === $other->id;
  }

  public function esMismoEstado(EstadoBuilder $estadoAAgregar): bool {
    return false; // Stub, as in Java
  }
}