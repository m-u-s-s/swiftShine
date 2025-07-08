<?php


namespace App\Livewire\Employe;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\Disponibilite;
use Illuminate\Support\Facades\Auth;

class CalendrierEmploye extends Component
{
    public $events = [];

    public function mount()
    {
        $user = Auth::user();

        // Les rendez-vous (avec buffer)
        foreach ($user->rendezvousEnTantQuEmploye as $rdv) {
            $debut = $rdv->date . 'T' . $rdv->heure;
            $fin = \Carbon\Carbon::parse($debut)
                ->addHours($rdv->duree)
                ->addMinutes(30)
                ->format('Y-m-d\TH:i:s');

            $this->events[] = [
                'title' => 'RDV - ' . $rdv->client->name,
                'start' => $debut,
                'end' => $fin,
                'color' => 'red',
            ];
        }

        // Les disponibilités
        foreach ($user->disponibilites as $dispo) {
            $this->events[] = [
                'title' => 'Disponible',
                'start' => $dispo->date . 'T' . $dispo->heure_debut,
                'end' => $dispo->date . 'T' . $dispo->heure_fin,
                'color' => 'green',
            ];
        }
    }

    public function render()
    {
        return view('livewire.calendrier-employe');
    }
}
