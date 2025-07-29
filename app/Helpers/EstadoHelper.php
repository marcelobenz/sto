<?php

namespace App\Helpers;

use App\Builders\EstadoBuilder;
use App\Models\ConfiguracionEstadoTramite;
use Illuminate\Support\Collection;

class EstadoHelper
{
    public static function buscarEstado(ConfiguracionEstadoTramite $config, Collection $estadosBuilder): ?EstadoBuilder
    {
        return $estadosBuilder->first(fn ($builder) => $builder->id === $config->id_proximo_estado);
    }
}
