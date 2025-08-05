<?php

namespace App\Interfaces;

use App\DTOs\UsuarioInternoDTO;

interface AsignableATramite {
  public function getDescripcion(): string;

  public function puedeAgregarAResumen(): bool;

  public function getId(): int;

  /**
   * @return UsuarioInternoDTO[]
   */
  public function getUsuarios(): array;

  public function getUsuarioQuePuedaSeguir(array $asignados): ?UsuarioInternoDTO;
}
