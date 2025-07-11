<?php

namespace App\Livewire;

use App\Models\RendezVous;
use Livewire\Component;
use App\Notifications\StatutRendezVousNotification;

class EmployeDashboard extends Component
{

    public function mettreAJourStatus($id, $nouveauStatus)
    {
        $rdv = RendezVous::findOrFail($id);
        $rdv->status = $nouveauStatus;
        $rdv->save();

        // ✅ Notifier le client
        $rdv->client->notify(new StatutRendezVousNotification($rdv));

        session()->flash('message', 'Status mis à jour et notification envoyée.');
    }

    public function render()
    {
        return view('livewire.employe-dashboard');
    }
}
