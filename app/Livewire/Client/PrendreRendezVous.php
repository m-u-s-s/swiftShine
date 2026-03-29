<?php

namespace App\Livewire\Client;

use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
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
        return User::select('id', 'name')
            ->where('role', 'employe')
            ->get();
    }

    public function getEmployeNomProperty()
    {
        return User::find($this->employe_id)?->name;
    }

    #[On('creneauChoisi')]
    public function setCreneau($date, $heure)
    {
        $this->rdvDate = $date;
        $this->rdvHeure = $heure;
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

        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function validerRdv()
    {
        if (!Auth::check()) {
            $this->addError('rdvDate', 'Vous devez être connecté pour réserver un rendez-vous.');
            return;
        }

        $existe = RendezVous::where('employe_id', $this->employe_id)
            ->where('date', $this->rdvDate)
            ->where('heure', $this->rdvHeure)
            ->whereIn('status', ['confirme', 'en_attente'])
            ->exists();

        if ($existe) {
            $this->addError('rdvDate', 'Ce créneau vient d’être réservé. Veuillez en choisir un autre.');
            return;
        }

        RendezVous::create([
            'client_id' => Auth::id(),
            'employe_id' => $this->employe_id,
            'date' => $this->rdvDate,
            'heure' => $this->rdvHeure,
            'status' => 'en_attente',
        ]);

        $this->reset(['step', 'employe_id', 'rdvDate', 'rdvHeure']);
        $this->step = 1;
        $this->rdvEnvoye = true;

        $this->dispatch('toast', 'Votre rendez-vous a été enregistré.', 'success');
    }

    public function render()
    {
        return view('livewire.client.prendre-rendez-vous', [
            'employes' => $this->employes,
            'employeNom' => $this->employeNom,
        ])->layout('layouts.app');
    }
}
