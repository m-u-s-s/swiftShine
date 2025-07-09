<?php

namespace App\Livewire\Employe;

use Livewire\Component;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Auth;

class ValidationMultipleRdv extends Component
{
    public $selection = [];

    public function toggleSelection($id)
    {
        if (in_array($id, $this->selection)) {
            $this->selection = array_diff($this->selection, [$id]);
        } else {
            $this->selection[] = $id;
        }
    }

    public function validerSelection()
    {
        RendezVous::whereIn('id', $this->selection)
            ->where('employe_id', Auth::id())
            ->update(['statut' => 'validé']);

        $this->selection = [];
        session()->flash('success', '✅ Rendez-vous validés avec succès.');
    }

    public function refuserSelection()
    {
        RendezVous::whereIn('id', $this->selection)
            ->where('employe_id', Auth::id())
            ->update(['statut' => 'refusé']);

        $this->selection = [];
        session()->flash('success', '❌ Rendez-vous refusés.');
    }

    public function render()
    {
        $rdvs = RendezVous::where('employe_id', Auth::id())
            ->where('statut', 'en attente')
            ->orderBy('date')
            ->get();

        return view('livewire.employe.validation-multiple-rdv', compact('rdvs'));
    }
}
