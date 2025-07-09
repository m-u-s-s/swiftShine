<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;

class AdminFeedbacks extends Component
{
    use WithPagination;

    public $employe_id = '';
    public $client_id = '';
    public $perPage = 5;
    public $statut = '';
    public $reponse = [];


    protected $queryString = ['employe_id', 'client_id', 'page'];

    public function updatedEmployeId()
    {
        $this->resetPage();
    }
    public function updatedClientId()
    {
        $this->resetPage();
    }

    public function exportPdf()
    {
        $url = route('admin.feedbacks.export', [
            'employe_id' => $this->employe_id,
            'client_id' => $this->client_id,
        ]);
        return Redirect::away($url);
    }

    public function exportCsv()
    {
        $url = route('admin.feedbacks.export.csv', [
            'employe_id' => $this->employe_id,
            'client_id' => $this->client_id,
        ]);
        return Redirect::away($url);
    }
    public function updatedReponse($id, $val)
    {
        Feedback::find($id)?->update(['reponse_admin' => $val]);
        $this->dispatch('toast', 'Réponse enregistrée', 'success');
    }
    public function filterByStatut($val)
    {
        $this->statut = $val;
        $this->resetPage();
    }


    public function render()
    {
        $feedbacks = Feedback::with(['client', 'rendezVous.employe'])
            ->when($this->employe_id, fn($q) =>
            $q->whereHas('rendezVous', fn($r) => $r->where('employe_id', $this->employe_id)))
            ->when($this->statut, fn($q) =>
            $q->whereHas('rendezVous', fn($r) =>
            $r->where('statut', $this->statut)))
            ->when($this->client_id, fn($q) =>
            $q->where('client_id', $this->client_id))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $employes = User::where('role', 'employe')->get();
        $clients  = User::where('role', 'client')->get();

        return view('livewire.admin-feedbacks', compact('feedbacks', 'employes', 'clients'));
    }
}
