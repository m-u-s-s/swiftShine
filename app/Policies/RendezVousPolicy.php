<?php

namespace App\Policies;

use App\Models\RendezVous;
use App\Models\User;

class RendezVousPolicy
{
    /**
     * Voir un rendez-vous précis.
     */
    public function view(User $user, RendezVous $rendezVous): bool
    {
        return match ($user->role) {
            'admin' => true,
            'client' => $rendezVous->client_id === $user->id,
            'employe' => $rendezVous->employe_id === $user->id,
            default => false,
        };
    }

    /**
     * Créer un rendez-vous.
     */
    public function create(User $user): bool
    {
        return $user->role === 'client';
    }

    /**
     * Modifier un rendez-vous.
     */
    public function update(User $user, RendezVous $rendezVous): bool
    {
        return match ($user->role) {
            'admin' => true,
            'client' => $rendezVous->client_id === $user->id,
            'employe' => $rendezVous->employe_id === $user->id,
            default => false,
        };
    }

    /**
     * Supprimer un rendez-vous.
     */
    public function delete(User $user, RendezVous $rendezVous): bool
    {
        return match ($user->role) {
            'admin' => true,
            'client' => $rendezVous->client_id === $user->id,
            default => false,
        };
    }
}