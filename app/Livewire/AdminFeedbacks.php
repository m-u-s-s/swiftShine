<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Feedback;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class AdminFeedbacks extends Component
{
    use WithPagination;

    public $employe_id = '';
    public $client_id = '';
    public $perPage = 5;
    public $status = '';
    public $reponse = [];

    protected $queryString = ['employe_id', 'client_id', 'status', 'page'];

    public function updatedEmployeId()
    {
        $this->resetPage();
    }

    public function updatedClientId()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function exportPdf()
    {
        Gate::authorize('export', Feedback::class);

        $url = route('admin.feedbacks.export', [
            'employe_id' => $this->employe_id,
            'client_id' => $this->client_id,
            'status' => $this->status,
        ]);

        return Redirect::away($url);
    }

    public function exportCsv()
    {
        Gate::authorize('export', Feedback::class);

        $url = route('admin.feedbacks.export.csv', [
            'employe_id' => $this->employe_id,
            'client_id' => $this->client_id,
            'status' => $this->status,
        ]);

        return Redirect::away($url);
    }

    public function updatedReponse($value, $key)
    {
        Gate::authorize('respond', Feedback::class);

        $feedback = Feedback::with(['client', 'rendezVous.employe'])->find($key);

        if (! $feedback) {
            $this->dispatch('toast', 'Feedback introuvable.', 'error');
            return;
        }

        $feedback->update([
            'reponse_admin' => $value,
        ]);

        ActivityLogger::log('feedback_repondu_par_admin', $feedback, [
            'feedback_id' => $feedback->id,
            'note' => $feedback->note,
            'client' => $feedback->client->name ?? null,
            'employe' => $feedback->rendezVous->employe->name ?? null,
        ]);

        $this->dispatch('toast', 'Réponse enregistrée.', 'success');
    }

    public function filterByStatus($val)
    {
        $this->status = $val;
        $this->resetPage();
    }

    public function render()
    {
        $feedbacks = Feedback::with(['client', 'rendezVous.employe'])
            ->when(
                $this->employe_id,
                fn ($q) => $q->whereHas('rendezVous', fn ($r) => $r->where('employe_id', $this->employe_id))
            )
            ->when(
                $this->status,
                fn ($q) => $q->whereHas('rendezVous', fn ($r) => $r->where('status', $this->status))
            )
            ->when(
                $this->client_id,
                fn ($q) => $q->where('client_id', $this->client_id)
            )
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $employes = User::where('role', 'employe')->orderBy('name')->get();
        $clients = User::where('role', 'client')->orderBy('name')->get();

        return view('livewire.admin-feedbacks', compact('feedbacks', 'employes', 'clients'));
    }
}