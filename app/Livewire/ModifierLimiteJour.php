<?php

namespace App\Livewire;

use App\Models\LimiteJournaliere;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModifierLimiteJour extends Component
{
    public $date;
    public $user_id;
    public $limite;
    public $fromAdmin = false;

    public function mount($date, $user_id, $fromAdmin = false)
    {
        $this->date = $date;
        $this->user_id = $user_id;
        $this->fromAdmin = $fromAdmin;

        $limiteExistante = LimiteJournaliere::where('user_id', $user_id)->where('date', $date)->first();
        $this->limite = $limiteExistante?->limite ?? null;
    }

    public function updatedLimite()
    {
        $record = LimiteJournaliere::firstOrNew([
            'user_id' => $this->user_id,
            'date' => $this->date,
        ]);

        // 🔒 Si verrouillé par un admin, et que l'utilisateur n'est pas admin, on empêche l'écriture
        if ($record->verrou_admin && !Auth::user()?->is_admin && !$this->fromAdmin) {
            $this->dispatch('toast', "Modification refusée : cette limite est verrouillée par un administrateur.", 'error');
            return;
        }

        $record->limite = $this->limite;

        // Si l'utilisateur est admin (ou accède via dashboard admin), on verrouille la limite
        if ($this->fromAdmin && Auth::user()?->is_admin) {
            $record->verrou_admin = true;
        }

        $record->save();

        $this->dispatch('toast', "Limite mise à jour.", 'success');
    }

    public function render()
    {
        return view('livewire.modifier-limite-jour');
    }
}
