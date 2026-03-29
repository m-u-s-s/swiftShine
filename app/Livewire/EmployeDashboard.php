<?php

namespace App\Livewire;

use App\Models\RendezVous;
use Livewire\Component;
use App\Notifications\StatutRendezVousNotification;
use Illuminate\Support\Facades\Gate;

class EmployeDashboard extends Component
{
    public function mettreAJourStatut($id, $nouveauStatus)
    {
        if (!in_array($nouveauStatus, ['confirme', 'refuse', 'en_attente'])) {
            abort(403);
        }

        $rdv = RendezVous::findOrFail($id);

        Gate::authorize('update', $rdv);

        $rdv->status = $nouveauStatus;
        $rdv->save();

        $rdv->client->notify(new StatutRendezVousNotification($rdv));

        session()->flash('message', 'Statut mis à jour et notification envoyée.');
    }

    public function render()
    {
        return view('livewire.employe-dashboard')->layout('layouts.app');
    }
}