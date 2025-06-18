<?php

namespace App\DTOs;

class EstadoTramiteDTO {
  private int $id;
  private string $nombre;
  private TipoEstadoEnum $tipoEstado;

  private int $puedeRechazar;
  private int $puedePedirDocumentacion;
  private int $puedeElegirCamino;
  private int $tieneExpediente;

  /** @var Collection<int, EstadoTramiteDTO> */
  private Collection $estadosAnteriores;

  /** @var Collection<int, EstadoTramiteDTO> */
  private Collection $estadosPosteriores;

  /** @var Collection<int, EstadoTramiteDTO> */
  private Collection $nodosAnteriores;

  public function __construct(
    string $id,
    string $nombre,
    TipoEstadoEnum $tipoEstado,
    string $puedeRechazar,
    string $puedePedirDocumentacion,
    string $puedeElegirCamino,
    string $tieneExpediente
  ) {
    $this->id = $id;
    $this->nombre = $nombre;
    $this->tipoEstado = $tipoEstado;
    $this->puedeRechazar = $puedeRechazar;
    $this->puedePedirDocumentacion = $puedePedirDocumentacion;
    $this->puedeElegirCamino = $puedeElegirCamino;
    $this->tieneExpediente = $tieneExpediente;
    // Initialize collections as empty Laravel Collections
    $this->estadosAnteriores = collect();
    $this->estadosPosteriores = collect();
    $this->nodosAnteriores = collect();
  }

  public function getId(): int {
    return $this->id;
  }

  public function setId(int $id): void {
    $this->id = $id;
  }

  public function getNombre(): string {
    return $this->nombre;
  }

  public function setNombre(string $nombre): void {
    $this->nombre = $nombre;
  }

  public function getTipoEstado(): TipoEstadoEnum {
    return $this->tipoEstado;
  }

  public function setTipoEstado(TipoEstadoEnum $tipoEstado): void {
    $this->tipoEstado = $tipoEstado;
  }


  public function getPuedeRechazar(): int {
    return $this->puedeRechazar;
  }

  public function setPuedeRechazar(int $puedeRechazar): void {
    $this->puedeRechazar = $puedeRechazar;
  }

  public function getPuedePedirDocumentacion(): int {
    return $this->puedePedirDocumentacion;
  }

  public function setPuedePedirDocumentacion(int $puedePedirDocumentacion): void {
    $this->puedePedirDocumentacion = $puedePedirDocumentacion;
  }

  public function getPuedeElegirCamino(): int {
    return $this->puedeElegirCamino;
  }

  public function setPuedeElegirCamino(int $puedeElegirCamino): void {
    $this->puedeElegirCamino = $puedeElegirCamino;
  }

  public function getTieneExpediente(): int {
    return $this->tieneExpediente;
  }

  public function setTieneExpediente(int $tieneExpediente): void {
    $this->tieneExpediente = $tieneExpediente;
  }

  public function getEstadosAnteriores(): Collection {
    return $this->estadosAnteriores;
  }

  public function setEstadosAnteriores(Collection $estadosAnteriores): void {
    $this->estadosAnteriores = $estadosAnteriores;
  }

  public function getEstadosPosteriores(): Collection {
    return $this->estadosPosteriores;
  }

  public function setEstadosPosteriores(Collection $estadosPosteriores): void {
    $this->estadosPosteriores = $estadosPosteriores;
  }

  public function getNodosAnteriores(): Collection {
    return $this->nodosAnteriores;
  }

  public function setNodosAnteriores(Collection $nodosAnteriores): void {
    $this->nodosAnteriores = $nodosAnteriores;
  }
}
