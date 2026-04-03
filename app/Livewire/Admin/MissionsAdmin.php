<?php

namespace App\Livewire\Admin;

use App\Models\RendezVous;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class MissionsAdmin extends Component
{
    use WithPagination;

    public $search = '';
    public $filtreEmploye = '';
    public $filtreStatus = '';
    public $filtrePriorite = '';
    public $tri = 'desc';

    protected $queryString = ['search', 'filtreEmploye', 'filtreStatus', 'filtrePriorite', 'page'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltreEmploye()
    {
        $this->resetPage();
    }

    public function updatingFiltreStatus()
    {
        $this->resetPage();
    }

    public function updatingFiltrePriorite()
    {
        $this->resetPage();
    }

    public function getEmployesProperty()
    {
        return User::where('role', 'employe')->orderBy('name')->get();
    }

    public function render()
    {
        $query = RendezVous::with(['client', 'employe'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('service_type', 'like', '%' . $this->search . '%')
                        ->orWhere('adresse', 'like', '%' . $this->search . '%')
                        ->orWhere('ville', 'like', '%' . $this->search . '%')
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('employe', fn ($e) => $e->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->filtreEmploye, fn ($q) => $q->where('employe_id', $this->filtreEmploye))
            ->when($this->filtreStatus, fn ($q) => $q->where('status', $this->filtreStatus))
            ->when($this->filtrePriorite, fn ($q) => $q->where('priorite', $this->filtrePriorite));

        return view('livewire.admin.missions-admin', [
            'missions' => $query->orderBy('date', $this->tri)->orderBy('heure', $this->tri)->paginate(10),
            'employes' => $this->employes,
        ])->layout('layouts.app');
    }
}