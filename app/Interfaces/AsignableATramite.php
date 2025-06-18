<?php

namespace App\Interfaces;

use App\Models\UsuarioInterno;

interface AsignableATramite {
  public function getDescripcion(): string;

  public function puedeAgregarAResumen(): bool;

  public function getId(): int;

  /**
   * @return UsuarioInterno[]
   */
  public function getUsuarios(): array;

  public function getUsuarioQuePuedaSeguir(array $asignados): ?UsuarioInterno;
}
