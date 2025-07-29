<?php

namespace App\Notificaciones;

use App\DTOs\HistorialTramiteDTO;
use App\Models\Multinota;
use App\Models\Notificacion;

class ComentarioNotificacion
{
    public function __construct(
        private string $comentario,
        private array $observers,
        private bool $notificaContribuyente
    ) {}

    public function guardarInicioTramite(HistorialTramiteDTO $dto, Multinota $tramite): void
    {
        if ($this->notificaContribuyente) {
            Notificacion::create([
                'id_historial_tramite' => $dto->getIdHistorialTramite(),
            ]);
        }

        foreach ($this->observers as $observer) {
            $observer->procesarComentario($tramite, $this->comentario);
        }
    }
}
