<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Feedback;

class FeedbackStats extends Component
{
    public $moyenne = 0;
    public $total = 0;

    public function mount()
    {
        $this->moyenne = round(Feedback::avg('note'), 2);
        $this->total = Feedback::count();
    }

    public function render()
    {
        return view('livewire.admin.feedback-stats');
    }
}
