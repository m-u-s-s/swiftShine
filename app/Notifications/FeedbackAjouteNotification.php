<?php

namespace App\Notifications;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FeedbackAjouteNotification extends Notification
{
    use Queueable;

    public $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'client' => $this->feedback->client->name,
            'employe' => $this->feedback->rendezVous->employe->name ?? '—',
            'note' => $this->feedback->note,
            'commentaire' => $this->feedback->commentaire,
            'feedback_id' => $this->feedback->id,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}