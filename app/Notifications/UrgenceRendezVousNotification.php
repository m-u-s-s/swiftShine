<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UrgenceRendezVousNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public RendezVous $rdv;

    public function __construct(RendezVous $rdv)
    {
        $this->rdv = $rdv;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚨 Intervention urgente en attente')
            ->line('Une demande urgente est toujours en attente de traitement.')
            ->line('Client : ' . ($this->rdv->client->name ?? '—'))
            ->line('Service : ' . ucfirst(str_replace('_', ' ', $this->rdv->service_type ?? 'service')))
            ->line('Date : ' . $this->rdv->date . ' à ' . $this->rdv->heure)
            ->action('Voir le tableau de bord admin', url('/admin/dashboard'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => '🚨 Une demande urgente est toujours en attente.',
            'rdv_id' => $this->rdv->id,
            'service_type' => $this->rdv->service_type,
            'priorite' => $this->rdv->priorite,
            'status' => $this->rdv->status,
        ];
    }
}