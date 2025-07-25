<?php

namespace App\Enums;

enum EstadoUsuarioInternoEnum: int {
  case INACTIVO = 0;
  case ACTIVO = 1;

  public function descripcion(): string {
    return match($this) {
      self::ACTIVO => 'Activo',
      self::INACTIVO => 'Inactivo',
    };
  }

  public static function fromId(?int $id): ?self {
    return match($id) {
      1 => self::ACTIVO,
      0 => self::INACTIVO,
      default => null,
    };
  }
}
