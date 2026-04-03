<?php

namespace App\Livewire\Client;

use App\Models\RendezVous;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class HistoriqueClient extends Component
{
    use WithPagination;

    public $search = '';
    public $tri = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'tri' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTri()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = RendezVous::with(['employe', 'feedback'])
            ->where('client_id', Auth::id())
            ->where('status', 'termine')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('service_type', 'like', '%' . $this->search . '%')
                        ->orWhere('adresse', 'like', '%' . $this->search . '%')
                        ->orWhere('ville', 'like', '%' . $this->search . '%')
                        ->orWhere('code_postal', 'like', '%' . $this->search . '%');
                });
            });

        return view('livewire.client.historique-client', [
            'historique' => $query
                ->orderBy('date', $this->tri)
                ->orderBy('heure', $this->tri)
                ->paginate(8),
        ])->layout('layouts.app');
    }
}