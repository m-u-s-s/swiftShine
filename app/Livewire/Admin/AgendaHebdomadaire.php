<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\User;
use Carbon\Carbon;

class AgendaHebdomadaire extends Component
{
    public $semaine; // ISO date (ex : 2025-07-08)
    public $employe_id = '';

    public function mount()
    {
        $this->semaine = now()->startOfWeek()->format('Y-m-d');
    }

    public function semainePrecedente()
    {
        $this->semaine = Carbon::parse($this->semaine)->subWeek()->startOfWeek()->format('Y-m-d');
    }

    public function semaineSuivante()
    {
        $this->semaine = Carbon::parse($this->semaine)->addWeek()->startOfWeek()->format('Y-m-d');
    }

    public function render()
    {
        $start = Carbon::parse($this->semaine)->startOfWeek();
        $end = $start->copy()->endOfWeek();

        $rdvs = RendezVous::with('employe', 'client')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->when($this->employe_id, fn($q) =>
                $q->where('employe_id', $this->employe_id))
            ->orderBy('date')->orderBy('heure')
            ->get();

        $jours = collect();
        foreach (range(0, 6) as $i) {
            $jour = $start->copy()->addDays($i);
            $jours->push([
                'label' => $jour->translatedFormat('l d/m'),
                'date' => $jour->toDateString(),
                'rdvs' => $rdvs->where('date', $jour->toDateString())
            ]);
        }

        $employes = User::where('role', 'employe')->get();

        return view('livewire.admin.agenda-hebdomadaire', compact('jours', 'employes'));
    }
}
