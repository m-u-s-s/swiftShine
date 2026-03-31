<?php

namespace App\Livewire;

use App\Models\RendezVous;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EmployeDashboard extends Component
{
    public function getMissionsDuJourProperty()
    {
        return RendezVous::with('client')
            ->where('employe_id', Auth::id())
            ->whereDate('date', today())
            ->whereIn('status', ['en_attente', 'confirme', 'en_route', 'sur_place', 'termine', 'refuse'])
            ->orderByRaw("
                CASE status
                    WHEN 'sur_place' THEN 1
                    WHEN 'en_route' THEN 2
                    WHEN 'confirme' THEN 3
                    WHEN 'en_attente' THEN 4
                    WHEN 'termine' THEN 5
                    WHEN 'refuse' THEN 6
                    ELSE 7
                END
            ")
            ->orderBy('heure')
            ->get();
    }

    public function getProchaineMissionProperty()
    {
        return RendezVous::with('client')
            ->where('employe_id', Auth::id())
            ->whereDate('date', today())
            ->whereIn('status', ['en_attente', 'confirme', 'en_route', 'sur_place'])
            ->orderBy('heure')
            ->first();
    }

    public function getHistoriqueRecentProperty()
    {
        return RendezVous::with('client')
            ->where('employe_id', Auth::id())
            ->where('status', 'termine')
            ->latest('mission_finished_at')
            ->limit(5)
            ->get();
    }

    public function getStatsJourProperty()
    {
        $missions = $this->missionsDuJour;

        return [
            'total' => $missions->count(),
            'a_faire' => $missions->whereIn('status', ['en_attente', 'confirme'])->count(),
            'en_cours' => $missions->whereIn('status', ['en_route', 'sur_place'])->count(),
            'terminees' => $missions->where('status', 'termine')->count(),
            'refusees' => $missions->where('status', 'refuse')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.employe-dashboard', [
            'missionsDuJour' => $this->missionsDuJour,
            'prochaineMission' => $this->prochaineMission,
            'historiqueRecent' => $this->historiqueRecent,
            'statsJour' => $this->statsJour,
        ])->layout('layouts.app');
    }
}