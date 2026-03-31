<?php

namespace App\Livewire;

use App\Models\RendezVous;
use App\Support\ActivityLogger;
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
        return RendezVous::with(['employe', 'feedback'])
            ->where('client_id', Auth::id())
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date', $this->tri)
            ->orderBy('heure', $this->tri)
            ->paginate(5);
    }

    public function getRendezVousPasseProperty()
    {
        return RendezVous::with(['employe', 'feedback'])
            ->where('client_id', Auth::id())
            ->whereDate('date', '<', now()->toDateString())
            ->orderByDesc('date')
            ->orderByDesc('heure')
            ->limit(6)
            ->get();
    }

    public function getDernierRendezVousProperty()
    {
        return RendezVous::where('client_id', Auth::id())
            ->latest('date')
            ->latest('heure')
            ->first();
    }

    public function getAdressesRecentesProperty()
    {
        return RendezVous::query()
            ->where('client_id', Auth::id())
            ->whereNotNull('adresse')
            ->where('adresse', '!=', '')
            ->select('adresse', 'ville', 'code_postal')
            ->distinct()
            ->latest('date')
            ->limit(5)
            ->get();
    }

    public function getStatsClientProperty()
    {
        $all = RendezVous::where('client_id', Auth::id())->get();

        return [
            'total' => $all->count(),
            'avenir' => $all->where('date', '>=', now()->toDateString())->count(),
            'termine' => $all->where('status', 'termine')->count(),
            'feedbacks' => $all->filter(fn($rdv) => $rdv->feedback)->count(),
        ];
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
        if (in_array($rdv->status, ['en_route', 'sur_place', 'termine', 'refuse'])) {
            $this->dispatch('toast', 'Ce rendez-vous ne peut plus être modifié.', 'error');
            return;
        }
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

        ActivityLogger::log('rdv_annule_par_client', $rdv, [
            'date' => $rdv->date?->format('Y-m-d') ?? (string) $rdv->date,
            'heure' => $rdv->heure,
            'service_type' => $rdv->service_type,
        ]);

        $rdv->delete();
        if (in_array($rdv->status, ['en_route', 'sur_place', 'termine', 'refuse'])) {
            $this->dispatch('toast', 'Ce rendez-vous ne peut plus être annulé.', 'error');
            return;
        }
        $this->dispatch('toast', 'Rendez-vous annulé.', 'error');
    }

    public function render()
    {
        return view('livewire.client-dashboard', [
            'avenir' => $this->rendezVousAvenir,
            'passe' => $this->rendezVousPasse,
            'total' => $this->statsClient['total'],
            'statsClient' => $this->statsClient,
            'dernierRendezVous' => $this->dernierRendezVous,
            'adressesRecentes' => $this->adressesRecentes,
        ])->layout('layouts.app');
    }
}
