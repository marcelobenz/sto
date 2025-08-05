<?php

namespace App\Interfaces;

use App\Builders\EstadoBuilder;

interface ValidadorEstado {
  public function validar(EstadoBuilder $estadoBuilder): void;
}
