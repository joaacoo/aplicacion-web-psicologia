<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatientNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Actualización de tu turno: ' . ($this->data['title'] ?? 'Novedades'))
            ->greeting('Hola ' . $notifiable->nombre . '!')
            ->line($this->data['mensaje'])
            ->action('Ver mi portal', $this->data['link'] ?? route('patient.dashboard'))
            ->line('¡Nos vemos pronto!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'mensaje' => $this->data['mensaje'],
            'link' => $this->data['link'] ?? '#',
            'type' => $notifiable->type ?? 'info'
        ];
    }
}
