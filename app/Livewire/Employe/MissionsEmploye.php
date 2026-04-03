<?php

namespace App\Livewire\Employe;

use Livewire\Component;

class MissionsEmploye extends Component
{
    public function render()
    {
        return view('livewire.employe.missions-employe')
            ->layout('layouts.app');
    }
}