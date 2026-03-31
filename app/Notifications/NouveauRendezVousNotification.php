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
        $service = ucfirst(str_replace('_', ' ', $this->rdv->service_type ?? 'service non précisé'));
        $priorite = ucfirst($this->rdv->priorite ?? 'normale');

        $mail = (new MailMessage)
            ->subject($this->rdv->priorite === 'urgente' ? '🚨 Demande urgente de nettoyage' : 'Nouvelle demande de nettoyage')
            ->line('Une nouvelle demande d’intervention a été envoyée.')
            ->line('Client : ' . ($this->rdv->client->name ?? '—'))
            ->line('Service : ' . $service)
            ->line('Date : ' . $this->rdv->date . ' à ' . $this->rdv->heure)
            ->line('Adresse : ' . ($this->rdv->adresse ?? '—') . ', ' . ($this->rdv->ville ?? '—'))
            ->line('Priorité : ' . $priorite)
            ->line('Animaux : ' . ($this->rdv->presence_animaux ? 'Oui' : 'Non'))
            ->line('Parking : ' . ($this->rdv->acces_parking ? 'Oui' : 'Non'))
            ->line('Matériel fourni : ' . ($this->rdv->materiel_fournit ? 'Oui' : 'Non'))
            ->line('Photos de référence : ' . (!empty($this->rdv->photos_reference) ? 'Oui' : 'Non'))
            ->action('Voir mes rendez-vous', url('/dashboard/employe'))
            ->line('Merci de confirmer ou refuser cette intervention rapidement.');

        if ($this->rdv->priorite === 'urgente') {
            $mail->line('⚠️ Cette demande a été marquée comme urgente.');
        }

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'message' => ($this->rdv->priorite === 'urgente'
                ? '🚨 Demande urgente : '
                : 'Nouvelle demande de nettoyage : ')
                . ucfirst(str_replace('_', ' ', $this->rdv->service_type ?? 'service')),
            'rdv_id' => $this->rdv->id,
            'client' => $this->rdv->client->name ?? '—',
            'date' => $this->rdv->date,
            'heure' => $this->rdv->heure,
            'adresse' => $this->rdv->adresse,
            'ville' => $this->rdv->ville,
            'priorite' => $this->rdv->priorite,
            'status' => $this->rdv->status,
            'has_photos' => !empty($this->rdv->photos_reference),
        ];
    }
}