<?php

namespace App\Services;

use App\Enums\TipoEstadoEnum;
use App\Models\Tramite;
use App\Models\TramiteEstadoTramite;

class TramiteService
{
    public function cantidadDeTramites(int $usuarioId): int
    {
        return TramiteEstadoTramite::query()
          // usuario en cuestión
            ->where('id_usuario_interno', $usuarioId)

          // registro aún no marcado como completo
            ->where('completo', 0)

          // TRÁMITE: no rechazado ni cancelado
            ->whereHas(
                'tramite',
                fn ($q) => $q
                    ->where('flag_rechazado', 0)
                    ->where('flag_cancelado', 0)
            )

          // ESTADO_TRAMITE: distinto de A_FINALIZAR
            ->whereHas(
                'estadoTramite',
                fn ($q) => $q
                    ->where('tipo', '!=', TipoEstadoEnum::A_FINALIZAR)   // o 'A_FINALIZAR' como string si no usas enum
            )
            ->count();
    }
}
