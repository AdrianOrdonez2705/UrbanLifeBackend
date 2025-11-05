<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuperarContrasenia extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $resetUrl; // URL completa para el reset de contraseña

    /**
     * Create a new message instance.
     */
    public function __construct($usuario, $resetUrl)
    {
        $this->usuario = $usuario;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuperar Contraseña',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.recuperar-contrasenia',
            with: [
                'usuario' => $this->usuario,
                'resetUrl' => $this->resetUrl, // Pasamos la URL al Blade
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
