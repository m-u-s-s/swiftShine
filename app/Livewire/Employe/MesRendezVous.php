<?php

namespace App\Livewire;

use App\Models\RendezVous;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class ClientDashboard extends Component
{
    use WithPagination;

    public $tri = 'asc';
    public $editRdvId = null;
    public $editDate = null;
    public $editHeure = null;

    public function getRendezVousAvenirProperty()
    {
        return RendezVous::where('client_id', Auth::id())
            ->whereDate('date', '>=', now())
            ->orderBy('date', $this->tri)
            ->paginate(5);
    }

    public function getRendezVousPasseProperty()
    {
        return RendezVous::where('client_id', Auth::id())
            ->whereDate('date', '<', now())
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
    }

    public function modifier($id)
    {
        $rdv = RendezVous::findOrFail($id);

        Gate::authorize('update', $rdv);

        $this->editRdvId = $rdv->id;
        $this->editDate = $rdv->date;
        $this->editHeure = $rdv->heure;
    }

    public function fermerEdition()
    {
        $this->editRdvId = null;
        $this->editDate = null;
        $this->editHeure = null;
    }

    public function enregistrerModif()
    {
        $rdv = RendezVous::where('id', $this->editRdvId)
            ->where('client_id', Auth::id())
            ->firstOrFail();

        Gate::authorize('update', $rdv);

        $rdv->date = $this->editDate;
        $rdv->heure = $this->editHeure;
        $rdv->status = 'en_attente';
        $rdv->save();

        $this->fermerEdition();
        $this->dispatch('toast', 'Rendez-vous mis à jour.', 'success');
    }

    public function annuler($id)
    {
        $rdv = RendezVous::findOrFail($id);

        Gate::authorize('delete', $rdv);

        $rdv->delete();
        $this->dispatch('toast', 'Rendez-vous annulé.', 'error');
    }

    public function render()
    {
        $total = RendezVous::where('client_id', Auth::id())->count();

        return view('livewire.client-dashboard', [
            'avenir' => $this->rendezVousAvenir,
            'passe' => $this->rendezVousPasse,
            'total' => $total,
        ])->layout('layouts.app');
    }
}