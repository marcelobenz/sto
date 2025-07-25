<?php

namespace App\Notificaciones;

use Illuminate\Support\Facades\Mail;

use App\Interfaces\ComentarioObserver;
use App\Models\Multinota;

class NotificadorComentariosMail implements ComentarioObserver {
  public function procesarComentario(Multinota $tramite, string $comentario): void {
    try {
      Mail::to($tramite->correo)
        ->queue(new ComentarioAgregadoATramite($tramite, $comentario));
    } catch (\Exception $e) {
      // Loguear o manejar
      throw new \Exception('No es posible notificar el cambio de estado');
    }
  }
}