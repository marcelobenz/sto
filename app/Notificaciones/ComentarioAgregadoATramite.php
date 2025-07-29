<?php

namespace App\Notificaciones;

use App\Models\Multinota;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComentarioAgregadoATramite extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Multinota $tramite,
        public string $comentario
    ) {}

    public function build()
    {
        return $this->subject(
            'Se ha agregado un comentario en el trámite Nº '
            .$this->tramite->id_tramite
            .' de '.$this->tramite->tipoTramiteMultinota->nombre
        )
            ->view('emails.comentario_agregado')
            ->with([
                'comentario' => $this->comentario,
                'tramite' => $this->tramite,
            ]);
    }
}
