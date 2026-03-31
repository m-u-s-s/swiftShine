<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatutRendezVousNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public RendezVous $rdv;

    public function __construct(RendezVous $rdv)
    {
        $this->rdv = $rdv;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $statusText = match ($this->rdv->status) {
            'confirme' => 'confirmée ✅',
            'refuse' => 'refusée ❌',
            'en_attente' => 'mise en attente ⏳',
            'en_route' => 'en route 🚗',
            'sur_place' => 'en cours sur place 📍',
            'termine' => 'terminée ✅',
            default => 'mise à jour',
        };

        $service = ucfirst(str_replace('_', ' ', $this->rdv->service_type ?? 'service'));
        $adresse = trim(($this->rdv->adresse ?? '') . ', ' . ($this->rdv->ville ?? ''), ', ');

        $mail = (new MailMessage)
            ->subject('Mise à jour de votre demande de nettoyage')
            ->line("Votre demande de {$service} a été {$statusText}.")
            ->line('Date : ' . $this->rdv->date . ' à ' . $this->rdv->heure)
            ->line('Lieu : ' . ($adresse ?: 'Adresse non précisée'));

        if ($this->rdv->status === 'en_route') {
            $mail->line('Notre employé est en route vers votre adresse.');
        }

        if ($this->rdv->status === 'sur_place') {
            $mail->line('L’intervention a commencé sur place.');
        }

        if ($this->rdv->status === 'termine') {
            $mail->line('L’intervention est terminée. Merci pour votre confiance.');
        }

        return $mail->action('Voir mon espace client', url('/dashboard/client'));
    }

    public function toArray($notifiable)
    {
        $statusText = match ($this->rdv->status) {
            'confirme' => 'confirmée',
            'refuse' => 'refusée',
            'en_attente' => 'mise en attente',
            'en_route' => 'en route',
            'sur_place' => 'en cours sur place',
            'termine' => 'terminée',
            default => 'mise à jour',
        };

        return [
            'message' => 'Votre demande de nettoyage a été ' . $statusText . '.',
            'rdv_id' => $this->rdv->id,
            'service_type' => $this->rdv->service_type,
            'date' => $this->rdv->date,
            'heure' => $this->rdv->heure,
            'status' => $this->rdv->status,
            'adresse' => $this->rdv->adresse,
            'ville' => $this->rdv->ville,
        ];
    }
}