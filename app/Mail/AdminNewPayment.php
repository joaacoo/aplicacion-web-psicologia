<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewPayment extends Mailable
{
    use Queueable, SerializesModels;

    public $patientName;
    public $appointmentDate;

    public function __construct($patientName, $appointmentDate)
    {
        $this->patientName = $patientName;
        $this->appointmentDate = $appointmentDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo Comprobante de Pago Subido',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_new_payment',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
