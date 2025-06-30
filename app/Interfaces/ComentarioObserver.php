<?php

namespace App\Interfaces;

use App\Models\Multinota;

interface ComentarioObserver {
  public function procesarComentario(Multinota $tramite, string $comentario): void;
}