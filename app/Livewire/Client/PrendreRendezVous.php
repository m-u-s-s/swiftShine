<?php

namespace App\Livewire\Client;

use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PrendreRendezVous extends Component
{
    public $step = 1;
    public $employe_id;
    public $rdvDate;
    public $rdvHeure;
    public $rdvEnvoye = false;

    public function getEmployesProperty()
    {
        return User::where('role', 'employe')->get();
    }

    public function getEmployeNomProperty()
    {
        return User::find($this->employe_id)?->name;
    }

    public function nextStep()
    {
        if ($this->step === 1 && !$this->employe_id) {
            $this->addError('employe_id', 'Veuillez choisir un employé.');
            return;
        }
        if ($this->step === 2 && (!$this->rdvDate || !$this->rdvHeure)) {
            $this->addError('rdvDate', 'Veuillez choisir une date et une heure.');
            return;
        }
        $this->step++;
    }

    public function prevStep()
    {
        $this->step--;
    }

    public function validerRdv()
    {
        RendezVous::create([
            'client_id' => Auth::id(),
            'employe_id' => $this->employe_id,
            'date' => $this->rdvDate,
            'heure' => $this->rdvHeure,
            'status' => 'en_attente',
        ]);

        $this->reset(['step', 'employe_id', 'rdvDate', 'rdvHeure']);
        $this->rdvEnvoye = true;

        $this->dispatch('toast', 'Votre rendez-vous a été enregistré.', 'success');
    }

    public function render()
    {
        return view('livewire.client.prendre-rendez-vous', [
            'employes' => $this->employes,
            'employeNom' => $this->employeNom,
        ]);
    }
}

