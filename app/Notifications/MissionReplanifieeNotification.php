<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissionReplanifieeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public RendezVous $rdv;
    public string $ancienEmploye;
    public string $ancienneDate;
    public string $ancienneHeure;

    public function __construct(RendezVous $rdv, string $ancienEmploye, string $ancienneDate, string $ancienneHeure)
    {
        $this->rdv = $rdv;
        $this->ancienEmploye = $ancienEmploye;
        $this->ancienneDate = $ancienneDate;
        $this->ancienneHeure = $ancienneHeure;
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
            ->subject('Votre intervention a été replanifiée')
            ->line("Votre demande de {$service} a été replanifiée par notre équipe.")
            ->line('Ancien créneau : ' . $this->ancienneDate . ' à ' . $this->ancienneHeure)
            ->line('Nouveau créneau : ' . $this->rdv->date . ' à ' . $this->rdv->heure)
            ->line('Employé assigné : ' . ($this->rdv->employe->name ?? '—'))
            ->line('Lieu : ' . ($adresse ?: 'Adresse non précisée'))
            ->action('Voir mon espace client', url('/dashboard/client'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Votre intervention a été replanifiée.',
            'rdv_id' => $this->rdv->id,
            'service_type' => $this->rdv->service_type,
            'ancienne_date' => $this->ancienneDate,
            'ancienne_heure' => $this->ancienneHeure,
            'nouvelle_date' => $this->rdv->date,
            'nouvelle_heure' => $this->rdv->heure,
            'employe' => $this->rdv->employe->name ?? null,
            'status' => $this->rdv->status,
        ];
    }
}