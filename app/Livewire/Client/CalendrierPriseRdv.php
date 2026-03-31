<?php

namespace App\Livewire\Client;

use App\Models\Disponibilite;
use App\Models\LimiteJournaliere;
use App\Models\RendezVous;
use Carbon\Carbon;
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
    public $dureeEstimee = 90;

    public function mount($employe_id, $selectedDate = null, $selectedHeure = null, $dureeEstimee = 90)
    {
        $this->employe_id = $employe_id;
        $this->selectedDate = $selectedDate;
        $this->selectedHeure = $selectedHeure;
        $this->dureeEstimee = $dureeEstimee ?: 90;

        $this->chargerDisponibilites();
    }

    public function updatedFiltreJour()
    {
        $this->chargerDisponibilites();
    }

    public function updatedDureeEstimee()
    {
        $this->chargerDisponibilites();
    }

    public function chargerDisponibilites()
    {
        $this->disponibilites = [];

        $debut = now()->startOfWeek()->addWeeks($this->semaineOffset);
        $fin = now()->endOfWeek()->addWeeks($this->semaineOffset);

        $raw = Disponibilite::where('user_id', $this->employe_id)
            ->whereBetween('date', [$debut->toDateString(), $fin->toDateString()])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('date');

        $rdvs = RendezVous::where('employe_id', $this->employe_id)
            ->whereBetween('date', [$debut->toDateString(), $fin->toDateString()])
            ->whereIn('status', ['confirme', 'en_attente'])
            ->get()
            ->groupBy('date');

        $limites = LimiteJournaliere::where('user_id', $this->employe_id)
            ->whereBetween('date', [$debut->toDateString(), $fin->toDateString()])
            ->get()
            ->keyBy('date');

        foreach ($raw as $jour => $disposJour) {
            if ($this->filtreJour && strtolower(date('l', strtotime($jour))) !== strtolower($this->filtreJour)) {
                continue;
            }

            $rdvsJour = $rdvs[$jour] ?? collect();
            $limite = $limites[$jour] ?? null;

            if ($limite && $limite->limite > 0 && $rdvsJour->count() >= $limite->limite) {
                continue;
            }

            foreach ($disposJour as $item) {
                $heureDebutDispo = Carbon::parse($jour . ' ' . $item->heure_debut);
                $heureFinDispo = Carbon::parse($jour . ' ' . $item->heure_fin);

                $cursor = $heureDebutDispo->copy();

                while ($cursor->copy()->addMinutes($this->dureeEstimee) <= $heureFinDispo) {
                    $start = $cursor->copy();
                    $end = $cursor->copy()->addMinutes($this->dureeEstimee);

                    $overlap = $rdvsJour->contains(function ($rdv) use ($jour, $start, $end) {
                        $rdvStart = Carbon::parse($jour . ' ' . $rdv->heure);
                        $rdvDuration = $rdv->duree ?? $rdv->duree_estimee ?? 90;
                        $rdvEnd = $rdvStart->copy()->addMinutes($rdvDuration);

                        return $start < $rdvEnd && $end > $rdvStart;
                    });

                    if (! $overlap) {
                        $this->disponibilites[$jour][] = $start->format('H:i');
                    }

                    $cursor->addMinutes(30);
                }
            }
        }
    }

    public function semaineSuivante()
    {
        $this->semaineOffset++;
        $this->chargerDisponibilites();
    }

    public function semainePrecedente()
    {
        if ($this->semaineOffset > 0) {
            $this->semaineOffset--;
            $this->chargerDisponibilites();
        }
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