<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RdvConfirmeNotification extends Notification
{
    use Queueable;

    public $rdv;

    public function __construct($rdv)
    {
        $this->rdv = $rdv;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rendez-vous confirmé')
            ->line('Votre rendez-vous a bien été confirmé.')
            ->line('Date : ' . $this->rdv->date . ' à ' . $this->rdv->heure)
            ->action('Voir mon dashboard', url('/dashboard/client'))
            ->line('Merci pour votre confiance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Votre rendez-vous du ' . $this->rdv->date . ' à ' . $this->rdv->heure . ' a été confirmé.',
            'rdv_id' => $this->rdv->id,
            'status' => 'confirme',
        ];
    }
}