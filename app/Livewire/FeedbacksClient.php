<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Feedback;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

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

    public function rendezVousSansFeedback()
    {
        return auth()->user()->rendezVous()
            ->where('statut', 'validé')
            ->whereDoesntHave('feedback')
            ->latest()
            ->get();
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
