<?php

namespace App\Helpers;

use App\Models\ConfiguracionEstadoTramite;
use Illuminate\Support\Collection;
use App\Builders\EstadoBuilder;

class EstadoHelper {
  public static function buscarEstado(ConfiguracionEstadoTramite $config, Collection $estadosBuilder): ?EstadoBuilder {
    return $estadosBuilder->first(fn($builder) => $builder->id === $config->id_proximo_estado);
  }
}
