<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeReaffectationSuggestionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public RendezVous $rdv,
        public string $employeSurcharge,
        public string $employeSuggere
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Suggestion automatique de réaffectation')
            ->line('Une mission pourrait être réaffectée pour équilibrer la charge.')
            ->line('Mission #' . $this->rdv->id)
            ->line('Employé surchargé : ' . $this->employeSurcharge)
            ->line('Employé suggéré : ' . $this->employeSuggere)
            ->action('Voir le dashboard admin', url('/admin/dashboard'));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Suggestion automatique de réaffectation pour la mission #' . $this->rdv->id,
            'rdv_id' => $this->rdv->id,
            'employe_surcharge' => $this->employeSurcharge,
            'employe_suggere' => $this->employeSuggere,
        ];
    }
}