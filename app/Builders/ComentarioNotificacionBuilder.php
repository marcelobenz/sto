<?php

namespace App\Builders;

use App\Notificaciones\ComentarioNotificacion;
use App\Notificaciones\NotificadorComentariosMail;

class ComentarioNotificacionBuilder {
  private string $comentario = '';
  private bool $notificaContribuyente = false;

  public function comentario(string $comentario): self {
    $this->comentario = $comentario;
    return $this;
  }

  public function notificaContribuyente(bool $flag): self {
    $this->notificaContribuyente = $flag;
    return $this;
  }

  public function build(): ComentarioNotificacion {
    if (trim($this->comentario) === '') {
      throw new \Exception('El comentario no puede estar vacío');
    }

    $observers = $this->notificaContribuyente
      ? [new NotificadorComentariosMail()]
      : [];

    return new ComentarioNotificacion($this->comentario, $observers, $this->notificaContribuyente);
  }
}