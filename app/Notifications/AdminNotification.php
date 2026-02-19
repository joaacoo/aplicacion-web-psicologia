<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification
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
            ->subject('Nueva notificación: ' . ($this->data['title'] ?? 'Soporte'))
            ->greeting('Hola soporte.')
            ->line($this->data['mensaje'])
            ->action('Ver en el portal', $this->data['link'] ?? route('admin.dashboard'))
            ->line('Gracias por usar la plataforma.')
            ->salutation('Lic. Nazarena De Luca');
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
            'type' => $this->data['type'] ?? 'info'
        ];
    }
}
