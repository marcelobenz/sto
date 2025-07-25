<?php

namespace App\DTOs;

class EstadoTramiteDTO {
  private int $id;
  private int $idEstadoTramite;
  private int $idGrupoInterno;
  private int $idUsuarioInterno;

  public function __construct(
    string $id,
    string $idEstadoTramite,
    string $idGrupoInterno,
    string $idUsuarioInterno
  ) {
    $this->id = $id;
    $this->idEstadoTramite = $idEstadoTramite;
    $this->idGrupoInterno = $idGrupoInterno;
    $this->idUsuarioInterno = $idUsuarioInterno;
  }

  public function getId(): int {
    return $this->id;
  }

  public function setId(int $id): void {
    $this->id = $id;
  }

  public function getIdEstadoTramite(): int {
    return $this->idEstadoTramite;
  }

  public function setIdEstadoTramite(int $idEstadoTramite): void {
    $this->idEstadoTramite = $idEstadoTramite;
  }

  public function getIdGrupoInterno(): int {
    return $this->idGrupoInterno;
  }

  public function setIdGrupoInterno(int $idGrupoInterno): void {
    $this->idGrupoInterno = $idGrupoInterno;
  }

  public function getIdUsuarioInterno(): int {
    return $this->idUsuarioInterno;
  }

  public function setIdUsuarioInterno(int $idUsuarioInterno): void {
    $this->idUsuarioInterno = $idUsuarioInterno;
  }
}
