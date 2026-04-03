<?php

namespace App\Livewire\Client;

use App\Models\RendezVous;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class MesRendezVousClient extends Component
{
    use WithPagination;

    public $tri = 'asc';
    public $filtreStatus = '';
    public $search = '';

    public $editRdvId = null;
    public $editDate = null;
    public $editHeure = null;

    protected $queryString = [
        'filtreStatus' => ['except' => ''],
        'search' => ['except' => ''],
        'tri' => ['except' => 'asc'],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltreStatus()
    {
        $this->resetPage();
    }

    public function updatingTri()
    {
        $this->resetPage();
    }

    public function modifier($id)
    {
        $rdv = RendezVous::findOrFail($id);

        Gate::authorize('update', $rdv);

        $this->editRdvId = $rdv->id;
        $this->editDate = $rdv->date?->format('Y-m-d') ?? $rdv->date;
        $this->editHeure = substr((string) $rdv->heure, 0, 5);
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

        if (in_array($rdv->status, ['en_route', 'sur_place', 'termine', 'refuse'])) {
            $this->dispatch('toast', 'Ce rendez-vous ne peut plus être modifié.', 'error');
            return;
        }

        $original = [
            'date' => $rdv->date,
            'heure' => $rdv->heure,
            'status' => $rdv->status,
            'priorite' => $rdv->priorite,
        ];

        $rdv->date = $this->editDate;
        $rdv->heure = $this->editHeure;
        $rdv->status = 'en_attente';

        $rdv->resetNotificationTrackingIfNeeded($original);
        $rdv->save();

        ActivityLogger::log('rdv_modifie_par_client', $rdv, [
            'ancienne_date' => $original['date']?->format('Y-m-d') ?? (string) $original['date'],
            'ancienne_heure' => $original['heure'],
            'nouvelle_date' => $rdv->date?->format('Y-m-d') ?? (string) $rdv->date,
            'nouvelle_heure' => $rdv->heure,
            'ancien_statut' => $original['status'],
            'nouveau_statut' => $rdv->status,
        ]);

        $this->fermerEdition();
        $this->dispatch('toast', 'Rendez-vous mis à jour.', 'success');
    }

    public function annuler($id)
    {
        $rdv = RendezVous::findOrFail($id);

        Gate::authorize('delete', $rdv);

        if (in_array($rdv->status, ['en_route', 'sur_place', 'termine', 'refuse'])) {
            $this->dispatch('toast', 'Ce rendez-vous ne peut plus être annulé.', 'error');
            return;
        }

        ActivityLogger::log('rdv_annule_par_client', $rdv, [
            'date' => $rdv->date?->format('Y-m-d') ?? (string) $rdv->date,
            'heure' => $rdv->heure,
            'service_type' => $rdv->service_type,
        ]);

        $rdv->delete();

        $this->dispatch('toast', 'Rendez-vous annulé.', 'error');
    }

    public function render()
    {
        $query = RendezVous::with(['employe', 'feedback'])
            ->where('client_id', Auth::id())
            ->when($this->filtreStatus, fn ($q) => $q->where('status', $this->filtreStatus))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('service_type', 'like', '%' . $this->search . '%')
                        ->orWhere('adresse', 'like', '%' . $this->search . '%')
                        ->orWhere('ville', 'like', '%' . $this->search . '%')
                        ->orWhere('code_postal', 'like', '%' . $this->search . '%');
                });
            });

        return view('livewire.client.mes-rendez-vous-client', [
            'rendezVous' => $query
                ->orderBy('date', $this->tri)
                ->orderBy('heure', $this->tri)
                ->paginate(8),
        ])->layout('layouts.app');
    }
}