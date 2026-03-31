<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RappelRendezVousNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public RendezVous $rdv;
    public string $timing;

    public function __construct(RendezVous $rdv, string $timing = '24h')
    {
        $this->rdv = $rdv;
        $this->timing = $timing;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $service = ucfirst(str_replace('_', ' ', $this->rdv->service_type ?? 'service'));
        $adresse = trim(($this->rdv->adresse ?? '') . ', ' . ($this->rdv->ville ?? ''), ', ');

        return (new MailMessage)
            ->subject('Rappel de votre intervention de nettoyage')
            ->line("Petit rappel : votre {$service} est prévu dans {$this->timing}.")
            ->line('Date : ' . $this->rdv->date . ' à ' . $this->rdv->heure)
            ->line('Adresse : ' . ($adresse ?: 'Adresse non précisée'))
            ->action('Voir mon espace client', url('/dashboard/client'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Rappel : votre intervention est prévue dans {$this->timing}.",
            'rdv_id' => $this->rdv->id,
            'timing' => $this->timing,
            'date' => $this->rdv->date,
            'heure' => $this->rdv->heure,
            'service_type' => $this->rdv->service_type,
        ];
    }
}