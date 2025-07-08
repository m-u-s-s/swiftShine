<?php

namespace App\Livewire\Employe;

use Livewire\Component;
use App\Models\Disponibilite;
use Illuminate\Support\Facades\Auth;

class DisponibilitesManager extends Component
{
    public $date;
    public $heure_debut;
    public $heure_fin;

    public function ajouter()
    {
        $this->validate([
            'date' => 'required|date|after_or_equal:today',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        Disponibilite::create([
            'user_id' => Auth::id(),
            'date' => $this->date,
            'heure_debut' => $this->heure_debut,
            'heure_fin' => $this->heure_fin,
        ]);

        $this->reset(['date', 'heure_debut', 'heure_fin']);
        session()->flash('message', 'Créneau ajouté !');
    }

    public function supprimer($id)
    {
        Disponibilite::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();
    }

    public function render()
    {
        $dispos = Disponibilite::where('user_id', Auth::id())
            ->orderBy('date')
            ->get();

        return view('livewire.employe.disponibilites-manager', [
            'disponibilites' => $dispos,
        ]);
    }
}

