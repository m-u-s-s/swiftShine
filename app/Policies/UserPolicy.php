<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Gérer les utilisateurs.
     */
    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Changer le rôle d'un utilisateur.
     */
    public function updateRole(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Activer / désactiver un utilisateur.
     */
    public function toggleActivation(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Importer des données.
     */
    public function import(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Exporter des données.
     */
    public function export(User $user): bool
    {
        return $user->role === 'admin';
    }
}