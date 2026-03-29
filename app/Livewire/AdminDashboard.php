<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\RendezVous;

class AdminDashboard extends Component
{
    public $filtreEmploye = null;
    public $statistiquesData = [];
    public $statsMensuelles = [];
    public $rdvs = [];
    public $employes;
    public $clients;
    public $employeSelectionne = null;

    public function mount()
    {
        $this->employes = User::where('role', 'employe')->get();
        $this->clients = User::where('role', 'client')->get();
        $this->mettreAJourStats();
        $this->chargerRdvs();
    }

    public function updatedFiltreEmploye()
    {
        $this->mettreAJourStats();
        $this->chargerRdvs();
    }

    public function mettreAJourStats()
    {
        $query = RendezVous::query();

        if ($this->filtreEmploye) {
            $query->where('employe_id', $this->filtreEmploye);
        }

        $this->statistiquesData = [
            'confirme' => (clone $query)->where('status', 'confirme')->count(),
            'attente' => (clone $query)->where('status', 'en_attente')->count(),
            'refuse' => (clone $query)->where('status', 'refuse')->count(),
        ];

        $this->statsMensuelles = collect(range(1, 12))->map(function ($mois) use ($query) {
            return (clone $query)->whereMonth('date', $mois)->count();
        })->toArray();

        $this->dispatch('updateChartData', data: $this->statistiquesData);
        $this->dispatch('updateMonthlyChart', data: $this->statsMensuelles);
    }

    public function chargerRdvs()
    {
        $query = RendezVous::with('client', 'employe');

        if ($this->filtreEmploye) {
            $query->where('employe_id', $this->filtreEmploye);
        }

        $this->rdvs = $query->get()->map(function ($rdv) {
            return [
                'title' => $rdv->client->name . ' → ' . $rdv->employe->name,
                'start' => $rdv->date . 'T' . $rdv->heure,
                'color' => match ($rdv->status) {
                    'confirme' => '#22c55e',
                    'refuse' => '#ef4444',
                    'en_attente' => '#facc15',
                    default => '#60a5fa',
                },
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.admin-dashboard', [
            'employes' => $this->employes,
            'clients' => $this->clients,
            'stats' => $this->statistiquesData,
            'rdvs' => $this->rdvs,
        ])->layout('layouts.app');
    }
}
