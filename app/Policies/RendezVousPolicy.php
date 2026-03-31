<?php

namespace App\Policies;

use App\Models\RendezVous;
use App\Models\User;

class RendezVousPolicy
{
    public function view(User $user, RendezVous $rendezVous): bool
    {
        return match ($user->role) {
            'admin' => true,
            'client' => $rendezVous->client_id === $user->id,
            'employe' => $rendezVous->employe_id === $user->id,
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->role === 'client';
    }

    public function update(User $user, RendezVous $rendezVous): bool
    {
        return match ($user->role) {
            'admin' => true,
            'client' => $rendezVous->client_id === $user->id
                && in_array($rendezVous->status, ['en_attente', 'confirme']),
            'employe' => $rendezVous->employe_id === $user->id,
            default => false,
        };
    }

    public function delete(User $user, RendezVous $rendezVous): bool
    {
        return match ($user->role) {
            'admin' => true,
            'client' => $rendezVous->client_id === $user->id
                && in_array($rendezVous->status, ['en_attente', 'confirme']),
            default => false,
        };
    }
}