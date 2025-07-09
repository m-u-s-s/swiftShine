<?php

namespace App\Livewire\Employe;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;

class FeedbackStats extends Component
{
    public $moyenne = 0;
    public $total = 0;

    public function mount()
    {
        $feedbacks = Feedback::whereHas('rendezVous', fn($q) =>
            $q->where('employe_id', Auth::id()))->get();

        $this->moyenne = round($feedbacks->avg('note'), 2);
        $this->total = $feedbacks->count();
    }

    public function render()
    {
        return view('livewire.employe.feedback-stats');
    }
}
