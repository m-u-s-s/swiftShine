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
        $this->moyenne = round(
            Feedback::whereHas(
                'rendezVous',
                fn($q) =>
                $q->where('employe_id', Auth::id())
            )->avg('note'),
            2
        );

        $this->total = Feedback::whereHas(
            'rendezVous',
            fn($q) =>
            $q->where('employe_id', Auth::id())
        )->count();
    }

    public function render()
    {
        return view('livewire.employe.feedback-stats');
    }
}
