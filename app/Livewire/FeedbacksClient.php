<?php

namespace App\Livewire;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class FeedbacksClient extends Component
{
    use WithPagination;

    public $noteMin = 1;
    public $sort = 'desc';

    public function updatingNoteMin()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function supprimer($id)
    {
        $feedback = Feedback::findOrFail($id);

        Gate::authorize('delete', $feedback);

        $feedback->delete();

        $this->dispatch('toast', 'Feedback supprimé.', 'success');
    }

    public function render()
    {
        $feedbacks = Feedback::with('rendezVous.employe')
            ->where('client_id', Auth::id())
            ->where('note', '>=', $this->noteMin)
            ->orderBy('created_at', $this->sort)
            ->paginate(5);

        return view('livewire.feedbacks-client', compact('feedbacks'));
    }
}