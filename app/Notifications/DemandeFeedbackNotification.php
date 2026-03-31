<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeFeedbackNotification extends Notification implements ShouldQueue
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
        $service = ucfirst(str_replace('_', ' ', $this->rdv->service_type ?? 'service'));

        return (new MailMessage)
            ->subject('Comment s’est passée votre intervention ?')
            ->line("Votre {$service} a bien eu lieu ?")
            ->line('Votre avis nous aide à améliorer la qualité de nos prestations.')
            ->action('Laisser un feedback', url('/feedback/ajouter/' . $this->rdv->id))
            ->line('Merci pour votre confiance.');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Merci de laisser votre avis sur votre intervention récente.',
            'rdv_id' => $this->rdv->id,
            'service_type' => $this->rdv->service_type,
        ];
    }
}