<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouveauRendezVousNotification extends Notification
{
    use Queueable;

    public $rdv;

    public function __construct($rdv)
    {
        $this->rdv = $rdv;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouveau rendez-vous en attente')
            ->line('Un nouveau rendez-vous a été demandé par : ' . $this->rdv->client->name)
            ->line('Date : ' . $this->rdv->date . ' à ' . $this->rdv->heure)
            ->action('Gérer le rendez-vous', url('/dashboard/employe'))
            ->line('Merci d’agir rapidement.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Nouveau RDV demandé par ' . $this->rdv->client->name,
            'rdv_id' => $this->rdv->id,
            'date' => $this->rdv->date,
            'heure' => $this->rdv->heure,
            'status' => $this->rdv->status,
        ];
    }
}