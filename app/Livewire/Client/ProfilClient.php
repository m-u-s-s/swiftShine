<?php

namespace App\Livewire\Client;

use App\Models\RendezVous;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfilClient extends Component
{
    public function getClientProperty()
    {
        return Auth::user();
    }

    public function getStatsProperty()
    {
        $rdvs = RendezVous::where('client_id', Auth::id())->get();

        return [
            'total' => $rdvs->count(),
            'termine' => $rdvs->where('status', 'termine')->count(),
            'avenir' => $rdvs->whereIn('status', ['en_attente', 'confirme', 'en_route', 'sur_place'])->count(),
            'urgentes' => $rdvs->where('priorite', 'urgente')->count(),
        ];
    }

    public function getAdressesRecentesProperty()
    {
        return RendezVous::query()
            ->where('client_id', Auth::id())
            ->whereNotNull('adresse')
            ->where('adresse', '!=', '')
            ->selectRaw('adresse, ville, code_postal, MAX(date) as last_date')
            ->groupBy('adresse', 'ville', 'code_postal')
            ->orderByDesc('last_date')
            ->limit(5)
            ->get();
    }

    public function getDernierePreferenceProperty()
    {
        return RendezVous::where('client_id', Auth::id())
            ->latest('date')
            ->latest('heure')
            ->first();
    }

    public function render()
    {
        return view('livewire.client.profil-client', [
            'client' => $this->client,
            'stats' => $this->stats,
            'adressesRecentes' => $this->adressesRecentes,
            'dernierePreference' => $this->dernierePreference,
        ])->layout('layouts.app');
    }
}
