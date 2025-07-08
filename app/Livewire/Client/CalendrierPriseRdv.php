<?php

namespace App\Livewire\Client;

use App\Models\Disponibilite;
use App\Models\LimiteJournaliere;
use App\Models\RendezVous;
use Livewire\Component;

class CalendrierPriseRdv extends Component
{
    public $employe_id;
    public $selectedDate;
    public $selectedHeure;
    public $disponibilites = [];
    public $employeNom;
    public $filtreJour = null;
    public $vueSemaine = true;
    public $semaineOffset = 0;
    public $confirmation = false;

    public function mount($employe_id, $selectedDate = null, $selectedHeure = null)
    {
        $this->employe_id = $employe_id;
        $this->selectedDate = $selectedDate;
        $this->selectedHeure = $selectedHeure;
        $this->chargerDisponibilites();
    }

    public function updatedFiltreJour()
    {
        $this->chargerDisponibilites();
    }

    public function chargerDisponibilites()
    {
        $debut = now()->startOfWeek()->addWeeks($this->semaineOffset);
        $fin = now()->endOfWeek()->addWeeks($this->semaineOffset);

        $raw = Disponibilite::where('user_id', $this->employe_id)
            ->whereBetween('date', [$debut, $fin])
            ->orderBy('date')
            ->orderBy('heure')
            ->get();


        foreach ($raw as $item) {
            $jour = $item->date;
            $heure = $item->heure;

            // Ne pas afficher les créneaux déjà réservés
            $dejaPris = RendezVous::where('employe_id', $this->employe_id)
                ->where('date', $jour)
                ->where('heure', $heure)
                ->whereIn('status', ['valide', 'en_attente'])
                ->exists();

            if ($dejaPris) continue;

            // Respecter la limite journalière s'il y en a une
            $limite = LimiteJournaliere::where('user_id', $this->employe_id)
                ->where('date', $jour)
                ->first();

            if ($limite && $limite->limite > 0) {
                $total = RendezVous::where('employe_id', $this->employe_id)
                    ->where('date', $jour)
                    ->whereIn('status', ['valide', 'en_attente'])
                    ->count();

                if ($total >= $limite->limite) continue;
            }

            if ($this->filtreJour && strtolower(date('l', strtotime($jour))) !== strtolower($this->filtreJour)) {
                continue;
            }

            $this->disponibilites[$jour][] = $heure;
        }
    }

    public function semaineSuivante()
    {
        $this->semaineOffset++;
        $this->chargerDisponibilites();
    }

    public function semainePrecedente()
    {
        $this->semaineOffset--;
        $this->chargerDisponibilites();
    }

    public function choisir($date, $heure)
    {
        $this->selectedDate = $date;
        $this->selectedHeure = $heure;
        $this->confirmation = true;
        $this->dispatch('creneauChoisi', date: $date, heure: $heure);
    }

    public function render()
    {
        return view('livewire.client.calendrier-prise-rdv');
    }
}
