<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserNotification extends Mailable
{
    use Queueable, SerializesModels;

    // Recibimos los datos que queremos enviar
    public function __construct(public string $titulo, public string $mensaje) {}

    public function build()
    {
        return $this->subject($this->titulo)
                    ->markdown('emails.user-notification');
    }
}