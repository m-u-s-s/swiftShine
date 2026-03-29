<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;

class FeedbackPolicy
{
    /**
     * Voir un feedback précis.
     */
    public function view(User $user, Feedback $feedback): bool
    {
        return match ($user->role) {
            'admin' => true,
            'client' => $feedback->client_id === $user->id,
            'employe' => $feedback->rendezVous?->employe_id === $user->id,
            default => false,
        };
    }

    /**
     * Créer un feedback.
     */
    public function create(User $user): bool
    {
        return $user->role === 'client';
    }

    /**
     * Modifier son propre feedback.
     */
    public function update(User $user, Feedback $feedback): bool
    {
        return $user->role === 'client'
            && $feedback->client_id === $user->id;
    }

    /**
     * Supprimer son propre feedback.
     */
    public function delete(User $user, Feedback $feedback): bool
    {
        return $user->role === 'client'
            && $feedback->client_id === $user->id;
    }

    /**
     * Répondre à un feedback côté admin.
     */
    public function respond(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Exporter les feedbacks côté admin.
     */
    public function export(User $user): bool
    {
        return $user->role === 'admin';
    }
}