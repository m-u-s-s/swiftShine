<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\RendezVous;

class StatutRendezVousNotification extends Notification
{
    public $rdv;

    public function __construct(RendezVous $rdv)
    {
        $this->rdv = $rdv;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // ✅ Email + Notification Livewire Jetstream
    }

    public function toMail($notifiable)
    {
        $statusText = match($this->rdv->status) {
            'valide' => 'confirmé ✅',
            'refuse' => 'refusé ❌',
            default => 'mis à jour',
        };

        return (new MailMessage)
            ->subject("Mise à jour de votre rendez-vous")
            ->line("Votre rendez-vous du {$this->rdv->date} à {$this->rdv->heure} a été {$statusText}.")
            ->action('Voir mon compte', url('/dashboard/client'));
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Votre rendez-vous du {$this->rdv->date} à {$this->rdv->heure} a été " . $this->rdv->status . '.',
            'rdv_id' => $this->rdv->id
        ];
    }
}
