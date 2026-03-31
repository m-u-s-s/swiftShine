<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;

class FeedbackPolicy
{
    public function view(User $user, Feedback $feedback): bool
    {
        return match ($user->role) {
            'admin' => true,
            'client' => $feedback->client_id === $user->id,
            'employe' => $feedback->rendezVous?->employe_id === $user->id,
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->role === 'client';
    }

    public function update(User $user, Feedback $feedback): bool
    {
        return $user->role === 'client'
            && $feedback->client_id === $user->id;
    }

    public function delete(User $user, Feedback $feedback): bool
    {
        return $user->role === 'client'
            && $feedback->client_id === $user->id;
    }

    public function respond(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function export(User $user): bool
    {
        return $user->role === 'admin';
    }
}