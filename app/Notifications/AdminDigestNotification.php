<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminDigestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $items = []
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Synthèse automatique des alertes métier')
            ->line('Voici les points nécessitant votre attention.');

        foreach ($this->items as $item) {
            $mail->line('• ' . $item);
        }

        return $mail
            ->action('Ouvrir le dashboard admin', url('/admin/dashboard'))
            ->line('Cette synthèse est générée automatiquement.');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Synthèse automatique des alertes métier disponible.',
            'items' => $this->items,
        ];
    }
}