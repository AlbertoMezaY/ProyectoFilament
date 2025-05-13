<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistroExitoso extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombre;

    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;
    }

    public function build()
    {
        return $this->subject('¡Registro exitoso!')
                    ->view('emails.registro-exitoso');
    }
}
