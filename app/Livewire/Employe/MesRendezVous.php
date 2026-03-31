<?php

namespace App\Livewire\Employe;

use App\Models\RendezVous;
use App\Notifications\StatutRendezVousNotification;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MesRendezVous extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $filtreStatus = null;
    public $priorite = null;
    public $tri = 'asc';
    public $search = '';

    public $showRapportModal = false;
    public $rapportRdvId = null;
    public $commentaire_fin_mission = '';
    public $duree_reelle = null;
    public $photos_apres = [];

    protected $listeners = [
        'refuser-rdv' => 'refuserRdv',
    ];

    public function mettreAJourStatut($id, $status)
    {
        if (!in_array($status, [
            'confirme',
            'refuse',
            'en_attente',
            'en_route',
            'sur_place',
        ])) {
            abort(403);
        }

        $rdv = RendezVous::findOrFail($id);

        Gate::authorize('update', $rdv);

        $original = [
            'date' => $rdv->date,
            'heure' => $rdv->heure,
            'status' => $rdv->status,
            'priorite' => $rdv->priorite,
        ];

        if ($status === 'en_route' && !$rdv->mission_started_at) {
            $rdv->mission_started_at = now();
        }

        $rdv->status = $status;

        $rdv->resetNotificationTrackingIfNeeded($original);
        $rdv->save();

        $rdv->client?->notify(new StatutRendezVousNotification($rdv));

        $message = match ($status) {
            'confirme' => 'Intervention confirmée.',
            'refuse' => 'Intervention refusée.',
            'en_route' => 'Intervention marquée en route.',
            'sur_place' => 'Intervention marquée sur place.',
            default => 'Statut mis à jour.',
        };

        $type = in_array($status, ['refuse']) ? 'error' : 'success';
        ActivityLogger::log('mission_statut_modifie', $rdv, [
            'ancien_statut' => $original['status'],
            'nouveau_statut' => $rdv->status,
            'date' => $rdv->date?->format('Y-m-d') ?? (string) $rdv->date,
            'heure' => $rdv->heure,
            'client' => $rdv->client->name ?? null,
        ]);
        $this->dispatch('toast', $message, $type);
    }

    public function ouvrirRapportFinMission($id)
    {
        $rdv = RendezVous::findOrFail($id);

        Gate::authorize('update', $rdv);

        $this->rapportRdvId = $rdv->id;
        $this->commentaire_fin_mission = $rdv->commentaire_fin_mission ?? '';
        $this->duree_reelle = $rdv->duree_reelle ?? $rdv->duree_estimee;
        $this->photos_apres = [];
        $this->showRapportModal = true;
    }

    public function fermerRapportFinMission()
    {
        $this->reset([
            'showRapportModal',
            'rapportRdvId',
            'commentaire_fin_mission',
            'duree_reelle',
            'photos_apres',
        ]);
    }

    public function sauverRapportFinMission()
    {
        $rdv = RendezVous::findOrFail($this->rapportRdvId);

        Gate::authorize('update', $rdv);

        $this->validate([
            'commentaire_fin_mission' => ['nullable', 'string', 'max:2000'],
            'duree_reelle' => ['required', 'integer', 'min:15', 'max:1440'],
            'photos_apres.*' => ['nullable', 'image', 'max:4096'],
        ]);

        $storedPhotos = $rdv->photos_apres ?? [];

        foreach ($this->photos_apres as $photo) {
            $storedPhotos[] = $photo->store('rendezvous/photos-apres', 'public');
        }

        $original = [
            'date' => $rdv->date,
            'heure' => $rdv->heure,
            'status' => $rdv->status,
            'priorite' => $rdv->priorite,
        ];

        $rdv->commentaire_fin_mission = $this->commentaire_fin_mission;
        $rdv->duree_reelle = $this->duree_reelle;
        $rdv->photos_apres = $storedPhotos;
        $rdv->mission_finished_at = now();
        $rdv->status = 'termine';

        $rdv->resetNotificationTrackingIfNeeded($original);
        $rdv->save();

        ActivityLogger::log('mission_terminee_avec_rapport', $rdv, [
            'duree_estimee' => $rdv->duree_estimee,
            'duree_reelle' => $rdv->duree_reelle,
            'has_commentaire_fin' => filled($rdv->commentaire_fin_mission),
            'has_photos_apres' => !empty($rdv->photos_apres),
        ]);

        $rdv->client?->notify(new StatutRendezVousNotification($rdv));

        $this->fermerRapportFinMission();
        $this->dispatch('toast', 'Rapport de fin de mission enregistré.', 'success');
    }

    public function refuserRdv($payload)
    {
        $this->mettreAJourStatut($payload['id'], 'refuse');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltreStatus()
    {
        $this->resetPage();
    }

    public function updatingPriorite()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = RendezVous::with('client')
            ->where('employe_id', Auth::id())
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('client', function ($clientQuery) {
                        $clientQuery->where('name', 'like', '%' . $this->search . '%');
                    })
                        ->orWhere('service_type', 'like', '%' . $this->search . '%')
                        ->orWhere('adresse', 'like', '%' . $this->search . '%')
                        ->orWhere('ville', 'like', '%' . $this->search . '%')
                        ->orWhere('telephone_client', 'like', '%' . $this->search . '%');
                });
            });

        if ($this->filtreStatus) {
            $query->where('status', $this->filtreStatus);
        }

        if ($this->priorite) {
            $query->where('priorite', $this->priorite);
        }

        return view('livewire.employe.mes-rendez-vous', [
            'rendezVous' => $query
                ->orderBy('date', $this->tri)
                ->orderBy('heure', $this->tri)
                ->paginate(5),
        ]);
    }
}
