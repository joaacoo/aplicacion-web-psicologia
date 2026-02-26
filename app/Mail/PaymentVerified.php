<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentVerified extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $fecha;
    public $link_reunion;
    public $appointment;

    /**
     * Create a new message instance.
     */
    public function __construct($nombre, $fecha, $link_reunion = null, $appointment = null)
    {
        $this->nombre = $nombre;
        $this->fecha = $fecha;
        $this->link_reunion = $link_reunion;
        $this->appointment = $appointment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu pago ha sido verificado - Turno Confirmado',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_verified',
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
