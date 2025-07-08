<?php

namespace App\Livewire\Employe;

use App\Models\RendezVous;
use App\Notifications\StatutRendezVousNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MesRendezVous extends Component
{
    use WithPagination;

    public $filtreStatut = null;
    public $tri = 'asc';
    public $search = '';
    protected $listeners = ['refuser-rdv' => 'refuserRdv'];

    public function mettreAJourStatut($id, $statut)
    {
        $rdv = RendezVous::findOrFail($id);
        $rdv->status = $statut;
        $rdv->save();

        $rdv->client->notify(new StatutRendezVousNotification($rdv));

        $this->dispatch('toast',
            $statut === 'valide' ? 'Rendez-vous confirmé.' : 'Rendez-vous refusé.',
            $statut === 'valide' ? 'success' : 'error'
        );
    }

    public function refuserRdv($payload)
    {
        $this->mettreAJourStatut($payload['id'], 'refuse');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltreStatut()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = RendezVous::where('employe_id', Auth::id())
            ->whereHas('client', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });

        if ($this->filtreStatut) {
            $query->where('status', $this->filtreStatut);
        }

        return view('livewire.employe.mes-rendez-vous', [
            'rendezVous' => $query->orderBy('date', $this->tri)->paginate(5)
        ]);
    }
}
