<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RestablecerClaveMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $newPassword;

    /**
     * Crea una nueva instancia de Mailable
     *
     * @param string $newPassword
     */
    public function __construct($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    /**
     * Genera el contenido del correo electrónico.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))
                    ->subject('Restablecimiento de Contraseña')
                    ->view('emails.restablecer_clave')
                    ->with('newPassword', $this->newPassword);
    }
}
