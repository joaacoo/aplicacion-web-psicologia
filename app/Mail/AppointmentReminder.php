<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $fecha;
    public $tipo_aviso; // recordatorio, ultimatum

    /**
     * Create a new message instance.
     */
    public function __construct($nombre, $fecha, $tipo_aviso = 'recordatorio')
    {
        $this->nombre = $nombre;
        $this->fecha = $fecha;
        $this->tipo_aviso = $tipo_aviso;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subj = ($this->tipo_aviso === 'ultimatum') ? '⚠️ ÚLTIMO AVISO: Pago pendiente de tu turno' : 'Recordatorio de pago - Tu turno está próximo';
        return new Envelope(
            subject: $subj,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment_reminder',
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
