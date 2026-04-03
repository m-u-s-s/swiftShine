<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\RendezVous;
use App\Models\ActivityLog;
use App\Models\LimiteJournaliere;
use App\Notifications\MissionReplanifieeNotification;
use App\Support\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Subscription;

class AdminDashboard extends Component
{
    public $filtreEmploye = null;
    public $statistiquesData = [];
    public $statsMensuelles = [];
    public $rdvs = [];
    public $employes;
    public $clients;
    public $employeSelectionne = null;

    public $selectedMissionId = null;
    public $showMissionModal = false;

    public $showPlanningModal = false;
    public $planningMissionId = null;
    public $planningEmployeId = null;
    public $planningDate = null;
    public $planningHeure = null;

    public $suggestedEmployees = [];

    public function mount()
    {
        $this->employes = User::where('role', 'employe')->orderBy('name')->get();
        $this->clients = User::where('role', 'client')->orderBy('name')->get();

        $this->mettreAJourStats();
        $this->chargerRdvs();
    }

    public function updatedFiltreEmploye()
    {
        $this->mettreAJourStats();
        $this->chargerRdvs();
    }

    protected function cacheKey(string $suffix): string
    {
        return 'admin_dashboard.' . ($this->filtreEmploye ?: 'all') . '.' . $suffix;
    }

    protected function clearAdminCache(): void
    {
        Cache::forget($this->cacheKey('statistiquesData'));
        Cache::forget($this->cacheKey('statsMensuelles'));
        Cache::forget($this->cacheKey('topServices'));
        Cache::forget($this->cacheKey('topVilles'));
        Cache::forget($this->cacheKey('dureeStats'));
        Cache::forget($this->cacheKey('performanceEmployes'));
        Cache::forget($this->cacheKey('feedbackRate'));
        Cache::forget($this->cacheKey('adminKpis'));
        Cache::forget($this->cacheKey('servicesSousEstimes'));
    }

    protected function logActivity(string $action, ?RendezVous $rdv = null, array $meta = []): void
    {
        ActivityLogger::log($action, $rdv, $meta);
    }

    public function mettreAJourStats()
    {
        $baseQuery = RendezVous::query();

        if ($this->filtreEmploye) {
            $baseQuery->where('employe_id', $this->filtreEmploye);
        }

        $this->statistiquesData = Cache::remember($this->cacheKey('statistiquesData'), now()->addMinutes(10), function () use ($baseQuery) {
            return [
                'confirme' => (clone $baseQuery)->where('status', 'confirme')->count(),
                'attente' => (clone $baseQuery)->where('status', 'en_attente')->count(),
                'refuse' => (clone $baseQuery)->where('status', 'refuse')->count(),
                'en_route' => (clone $baseQuery)->where('status', 'en_route')->count(),
                'sur_place' => (clone $baseQuery)->where('status', 'sur_place')->count(),
                'termine' => (clone $baseQuery)->where('status', 'termine')->count(),
            ];
        });

        $this->statsMensuelles = Cache::remember($this->cacheKey('statsMensuelles'), now()->addMinutes(10), function () use ($baseQuery) {
            return collect(range(1, 12))->map(function ($mois) use ($baseQuery) {
                return (clone $baseQuery)->whereMonth('date', $mois)->count();
            })->toArray();
        });

        $this->dispatch('updateChartData', data: $this->statistiquesData);
        $this->dispatch('updateMonthlyChart', data: $this->statsMensuelles);
    }

    public function chargerRdvs()
    {
        $query = RendezVous::with('client', 'employe');

        if ($this->filtreEmploye) {
            $query->where('employe_id', $this->filtreEmploye);
        }

        $this->rdvs = $query->get()->map(function ($rdv) {
            return [
                'title' => ($rdv->client->name ?? 'Client') . ' → ' . ($rdv->employe->name ?? 'Employé'),
                'start' => $rdv->date . 'T' . substr((string) $rdv->heure, 0, 5),
                'color' => match ($rdv->status) {
                    'confirme' => '#22c55e',
                    'refuse' => '#ef4444',
                    'en_attente' => '#facc15',
                    'en_route' => '#2563eb',
                    'sur_place' => '#4f46e5',
                    'termine' => '#047857',
                    default => '#60a5fa',
                },
            ];
        })->toArray();
    }

    public function getUrgencesProperty()
    {
        return RendezVous::with('client', 'employe')
            ->where('priorite', 'urgente')
            ->whereIn('status', ['en_attente', 'confirme', 'en_route', 'sur_place'])
            ->orderBy('date')
            ->orderBy('heure')
            ->limit(5)
            ->get();
    }

    public function getInterventionsDuJourProperty()
    {
        return RendezVous::with('client', 'employe')
            ->whereDate('date', today())
            ->orderBy('heure')
            ->limit(8)
            ->get();
    }

    public function getChargeEmployesProperty()
    {
        return User::where('role', 'employe')
            ->get()
            ->map(function ($employe) {
                $rdvsJour = RendezVous::where('employe_id', $employe->id)
                    ->whereDate('date', today())
                    ->whereIn('status', ['confirme', 'en_attente', 'en_route', 'sur_place'])
                    ->get();

                $totalMinutes = $rdvsJour->sum(function ($rdv) {
                    $duration = $rdv->duree ?? $rdv->duree_estimee ?? 90;
                    return $duration + 30;
                });

                return [
                    'employe' => $employe,
                    'count' => $rdvsJour->count(),
                    'minutes' => $totalMinutes,
                    'hours' => round($totalMinutes / 60, 1),
                ];
            })
            ->sortByDesc('minutes')
            ->values();
    }

    public function getMissionsTermineesProperty()
    {
        return RendezVous::with('client', 'employe')
            ->where('status', 'termine')
            ->orderByDesc('mission_finished_at')
            ->orderByDesc('date')
            ->limit(6)
            ->get();
    }

    public function getQualiteMissionsProperty()
    {
        return RendezVous::with('client', 'employe')
            ->where('status', 'termine')
            ->orderByDesc('mission_finished_at')
            ->limit(12)
            ->get()
            ->map(function ($rdv) {
                $estimated = $rdv->duree_estimee ?? $rdv->duree ?? null;
                $real = $rdv->duree_reelle;

                $difference = null;
                if (!is_null($estimated) && !is_null($real)) {
                    $difference = $real - $estimated;
                }

                return [
                    'rdv' => $rdv,
                    'has_report' => filled($rdv->commentaire_fin_mission),
                    'has_after_photos' => !empty($rdv->photos_apres),
                    'estimated' => $estimated,
                    'real' => $real,
                    'difference' => $difference,
                    'is_long_overrun' => !is_null($difference) && $difference >= 30,
                    'is_short_underrun' => !is_null($difference) && $difference <= -30,
                ];
            });
    }

    public function getQualiteStatsProperty()
    {
        $missions = RendezVous::where('status', 'termine')->get();

        return [
            'sans_rapport' => $missions->filter(fn($rdv) => blank($rdv->commentaire_fin_mission))->count(),
            'sans_photos_apres' => $missions->filter(fn($rdv) => empty($rdv->photos_apres))->count(),
            'avec_duree_reelle' => $missions->filter(fn($rdv) => !is_null($rdv->duree_reelle))->count(),
        ];
    }

    public function getRecentActivityLogsProperty()
    {
        return ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getTopServicesProperty()
    {
        return Cache::remember($this->cacheKey('topServices'), now()->addMinutes(10), function () {
            return RendezVous::query()
                ->selectRaw('service_type, COUNT(*) as total')
                ->whereNotNull('service_type')
                ->groupBy('service_type')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        });
    }

    public function getTopVillesProperty()
    {
        return Cache::remember($this->cacheKey('topVilles'), now()->addMinutes(10), function () {
            return RendezVous::query()
                ->selectRaw('ville, COUNT(*) as total')
                ->whereNotNull('ville')
                ->where('ville', '!=', '')
                ->groupBy('ville')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        });
    }

    public function getDureeStatsProperty()
    {
        return Cache::remember($this->cacheKey('dureeStats'), now()->addMinutes(10), function () {
            $missions = RendezVous::query()
                ->where('status', 'termine')
                ->whereNotNull('duree_estimee')
                ->whereNotNull('duree_reelle')
                ->get();

            if ($missions->isEmpty()) {
                return [
                    'avg_estimated' => null,
                    'avg_real' => null,
                    'avg_gap' => null,
                ];
            }

            return [
                'avg_estimated' => round($missions->avg('duree_estimee')),
                'avg_real' => round($missions->avg('duree_reelle')),
                'avg_gap' => round($missions->avg(fn($rdv) => $rdv->duree_reelle - $rdv->duree_estimee)),
            ];
        });
    }

    public function getPerformanceEmployesProperty()
    {
        return Cache::remember($this->cacheKey('performanceEmployes'), now()->addMinutes(10), function () {
            return User::where('role', 'employe')
                ->get()
                ->map(function ($employe) {
                    $missions = RendezVous::where('employe_id', $employe->id)
                        ->where('status', 'termine')
                        ->with('feedback')
                        ->get();

                    $avgGap = null;
                    $withDurations = $missions->filter(
                        fn($rdv) =>
                        !is_null($rdv->duree_estimee) && !is_null($rdv->duree_reelle)
                    );

                    if ($withDurations->isNotEmpty()) {
                        $avgGap = round($withDurations->avg(fn($rdv) => $rdv->duree_reelle - $rdv->duree_estimee));
                    }

                    $feedbacks = $missions->filter(fn($rdv) => $rdv->feedback)->pluck('feedback');
                    $avgNote = $feedbacks->isNotEmpty() ? round($feedbacks->avg('note'), 1) : null;

                    return [
                        'employe' => $employe,
                        'missions_terminees' => $missions->count(),
                        'avg_gap' => $avgGap,
                        'avg_note' => $avgNote,
                    ];
                })
                ->sortByDesc('missions_terminees')
                ->values()
                ->take(6);
        });
    }

    public function getFeedbackRateProperty()
    {
        return Cache::remember($this->cacheKey('feedbackRate'), now()->addMinutes(10), function () {
            $terminees = RendezVous::where('status', 'termine')->count();

            if ($terminees === 0) {
                return 0;
            }

            $avecFeedback = RendezVous::where('status', 'termine')
                ->whereHas('feedback')
                ->count();

            return round(($avecFeedback / $terminees) * 100);
        });
    }

    public function getUrgencesVieillissantesProperty()
    {
        return RendezVous::with('client', 'employe')
            ->where('priorite', 'urgente')
            ->where('status', 'en_attente')
            ->where('created_at', '<=', now()->subHours(4))
            ->orderBy('created_at')
            ->limit(5)
            ->get();
    }

    public function getServicesSousEstimesProperty()
    {
        return Cache::remember($this->cacheKey('servicesSousEstimes'), now()->addMinutes(10), function () {
            return RendezVous::query()
                ->where('status', 'termine')
                ->whereNotNull('service_type')
                ->whereNotNull('duree_estimee')
                ->whereNotNull('duree_reelle')
                ->get()
                ->groupBy('service_type')
                ->map(function ($items) {
                    return [
                        'avg_gap' => round($items->avg(fn($rdv) => $rdv->duree_reelle - $rdv->duree_estimee)),
                        'count' => $items->count(),
                    ];
                })
                ->filter(fn($row) => $row['count'] >= 3 && $row['avg_gap'] >= 20)
                ->sortByDesc('avg_gap');
        });
    }

    public function getAdminKpisProperty()
    {
        return Cache::remember($this->cacheKey('adminKpis'), now()->addMinutes(10), function () {
            $today = today();

            return [
                'en_attente' => RendezVous::where('status', 'en_attente')->count(),
                'urgentes_vieilles' => $this->urgencesVieillissantes->count(),
                'missions_longues' => $this->qualiteMissions->filter(fn($item) => $item['is_long_overrun'])->count(),
                'employes_surcharges' => $this->chargeEmployes->filter(fn($item) => $item['minutes'] >= 480)->count(),
                'missions_du_jour' => RendezVous::whereDate('date', $today)->count(),
                'missions_terminees_mois' => RendezVous::where('status', 'termine')
                    ->whereMonth('date', now()->month)
                    ->count(),
            ];
        });
    }

    public function getRecommendationsProperty()
    {
        $recommendations = collect();

        $surcharges = $this->chargeEmployes->filter(fn($item) => $item['minutes'] >= 480);
        foreach ($surcharges as $item) {
            $recommendations->push([
                'level' => 'danger',
                'title' => 'Employé surchargé',
                'message' => $item['employe']->name . ' dépasse 8h planifiées aujourd’hui.',
            ]);
        }

        foreach ($this->servicesSousEstimes->take(3) as $service => $row) {
            $recommendations->push([
                'level' => 'warning',
                'title' => 'Service sous-estimé',
                'message' => ucfirst(str_replace('_', ' ', $service)) . ' dépasse en moyenne l’estimé de ' . $row['avg_gap'] . ' min.',
            ]);
        }

        foreach ($this->topVilles->take(2) as $ville) {
            if ($ville->total >= 5) {
                $recommendations->push([
                    'level' => 'info',
                    'title' => 'Zone à forte demande',
                    'message' => $ville->ville . ' concentre actuellement ' . $ville->total . ' demandes.',
                ]);
            }
        }

        if ($this->feedbackRate < 40) {
            $recommendations->push([
                'level' => 'warning',
                'title' => 'Taux de feedback faible',
                'message' => 'Le taux de feedback est de ' . $this->feedbackRate . '%. Envisage une relance client plus forte.',
            ]);
        }

        foreach ($this->urgencesVieillissantes as $rdv) {
            $recommendations->push([
                'level' => 'danger',
                'title' => 'Urgence trop longtemps en attente',
                'message' => 'Mission urgente #' . $rdv->id . ' en attente depuis plus de 4h.',
            ]);
        }

        return $recommendations->take(8);
    }

    public function ouvrirMission(int $id): void
    {
        $this->selectedMissionId = $id;
        $this->showMissionModal = true;
        $this->suggestedEmployees = $this->computeSuggestedEmployees($id);
    }

    public function fermerMission(): void
    {
        $this->selectedMissionId = null;
        $this->showMissionModal = false;
        $this->suggestedEmployees = [];
    }

    public function getSelectedMissionProperty()
    {
        if (!$this->selectedMissionId) {
            return null;
        }

        return RendezVous::with('client', 'employe', 'feedback')->find($this->selectedMissionId);
    }

    public function ouvrirPlanning(int $id): void
    {
        $rdv = RendezVous::findOrFail($id);

        $this->planningMissionId = $rdv->id;
        $this->planningEmployeId = $rdv->employe_id;
        $this->planningDate = $rdv->date?->format('Y-m-d') ?? $rdv->date;
        $this->planningHeure = substr((string) $rdv->heure, 0, 5);
        $this->showPlanningModal = true;
        $this->suggestedEmployees = $this->computeSuggestedEmployees($id, $this->planningDate, $this->planningHeure);
    }

    public function updatedPlanningDate()
    {
        if ($this->planningMissionId && $this->planningDate && $this->planningHeure) {
            $this->suggestedEmployees = $this->computeSuggestedEmployees(
                $this->planningMissionId,
                $this->planningDate,
                $this->planningHeure
            );
        }
    }

    public function updatedPlanningHeure()
    {
        if ($this->planningMissionId && $this->planningDate && $this->planningHeure) {
            $this->suggestedEmployees = $this->computeSuggestedEmployees(
                $this->planningMissionId,
                $this->planningDate,
                $this->planningHeure
            );
        }
    }

    public function fermerPlanning(): void
    {
        $this->reset([
            'showPlanningModal',
            'planningMissionId',
            'planningEmployeId',
            'planningDate',
            'planningHeure',
        ]);

        $this->suggestedEmployees = [];
    }

    public function appliquerSuggestionEmploye(int $employeId): void
    {
        $this->planningEmployeId = $employeId;
    }

    public function enregistrerPlanning(): void
    {
        $this->validate([
            'planningMissionId' => ['required', 'exists:rendez_vous,id'],
            'planningEmployeId' => ['required', 'exists:users,id'],
            'planningDate' => ['required', 'date'],
            'planningHeure' => ['required'],
        ]);

        $rdv = RendezVous::with('client', 'employe')->findOrFail($this->planningMissionId);

        $ancienEmployeNom = $rdv->employe->name ?? 'Employé';
        $ancienneDate = $rdv->date?->format('Y-m-d') ?? (string) $rdv->date;
        $ancienneHeure = substr((string) $rdv->heure, 0, 5);
        $ancienEmployeId = $rdv->employe_id;
        $ancienStatus = $rdv->status;

        $newStart = Carbon::parse($this->planningDate . ' ' . $this->planningHeure);
        $newDuration = $rdv->duree ?? $rdv->duree_estimee ?? 90;
        $bufferMinutes = 30;
        $newEnd = $newStart->copy()->addMinutes($newDuration + $bufferMinutes);

        $conflict = RendezVous::where('id', '!=', $rdv->id)
            ->where('employe_id', $this->planningEmployeId)
            ->whereDate('date', $this->planningDate)
            ->whereIn('status', ['confirme', 'en_attente', 'en_route', 'sur_place'])
            ->get()
            ->contains(function ($other) use ($newStart, $newEnd, $bufferMinutes) {
                $otherStart = Carbon::parse($other->date . ' ' . $other->heure);
                $otherDuration = $other->duree ?? $other->duree_estimee ?? 90;
                $otherEnd = $otherStart->copy()->addMinutes($otherDuration + $bufferMinutes);

                return $newStart < $otherEnd && $newEnd > $otherStart;
            });

        if ($conflict) {
            $this->addError('planningHeure', 'Conflit détecté : cet employé a déjà une mission sur ce créneau.');
            return;
        }

        $original = [
            'date' => $rdv->date,
            'heure' => $rdv->heure,
            'status' => $rdv->status,
            'priorite' => $rdv->priorite,
        ];

        $rdv->employe_id = $this->planningEmployeId;
        $rdv->date = $this->planningDate;
        $rdv->heure = $this->planningHeure;

        if (in_array($rdv->status, ['confirme', 'en_route', 'sur_place'])) {
            $rdv->status = 'en_attente';
        }

        $rdv->resetNotificationTrackingIfNeeded($original);
        $rdv->save();
        $rdv->load('client', 'employe');

        if ($rdv->client) {
            $rdv->client->notify(
                new MissionReplanifieeNotification($rdv, $ancienEmployeNom, $ancienneDate, $ancienneHeure)
            );
        }

        if ($rdv->employe && $rdv->employe_id != $ancienEmployeId) {
            $rdv->employe->notify(
                new MissionReplanifieeNotification($rdv, $ancienEmployeNom, $ancienneDate, $ancienneHeure)
            );
        }

        $this->logActivity('mission_replanifiee', $rdv, [
            'ancienne_date' => $ancienneDate,
            'ancienne_heure' => $ancienneHeure,
            'nouvelle_date' => $rdv->date?->format('Y-m-d') ?? (string) $rdv->date,
            'nouvelle_heure' => $rdv->heure,
            'ancien_employe' => $ancienEmployeNom,
            'nouvel_employe' => $rdv->employe->name ?? 'Employé',
            'ancien_statut' => $ancienStatus,
            'nouveau_statut' => $rdv->status,
        ]);

        $this->clearAdminCache();
        $this->chargerRdvs();
        $this->mettreAJourStats();
        $this->fermerPlanning();

        $this->dispatch('toast', 'Mission replanifiée avec succès.', 'success');
    }

    protected function computeSuggestedEmployees(int $missionId, ?string $date = null, ?string $heure = null): array
    {
        $rdv = RendezVous::find($missionId);

        if (! $rdv) {
            return [];
        }

        $date = $date ?: ($rdv->date?->format('Y-m-d') ?? (string) $rdv->date);
        $heure = $heure ?: substr((string) $rdv->heure, 0, 5);

        return User::where('role', 'employe')
            ->get()
            ->map(function ($employe) use ($rdv, $date, $heure) {
                $score = $this->computeEmployeScore($employe->id, $date, $heure, $rdv);
                return [
                    'id' => $employe->id,
                    'name' => $employe->name,
                    'score' => $score['score'],
                    'load_minutes' => $score['load_minutes'],
                    'rdv_count' => $score['rdv_count'],
                    'has_conflict' => $score['has_conflict'],
                    'same_city_bonus' => $score['same_city_bonus'],
                ];
            })
            ->filter(fn($row) => ! $row['has_conflict'])
            ->sortBy('score')
            ->values()
            ->take(5)
            ->toArray();
    }

    protected function computeEmployeScore(int $employeId, string $date, string $heure, RendezVous $rdv): array
    {
        $bufferMinutes = 30;
        $duration = $rdv->duree ?? $rdv->duree_estimee ?? 90;
        $start = Carbon::parse($date . ' ' . $heure);
        $end = $start->copy()->addMinutes($duration + $bufferMinutes);

        $rdvsJour = RendezVous::where('employe_id', $employeId)
            ->whereDate('date', $date)
            ->whereIn('status', ['confirme', 'en_attente', 'en_route', 'sur_place'])
            ->get();

        $hasConflict = $rdvsJour->contains(function ($other) use ($start, $end, $bufferMinutes) {
            $otherStart = Carbon::parse($other->date . ' ' . $other->heure);
            $otherDuration = $other->duree ?? $other->duree_estimee ?? 90;
            $otherEnd = $otherStart->copy()->addMinutes($otherDuration + $bufferMinutes);

            return $start < $otherEnd && $end > $otherStart;
        });

        $loadMinutes = $rdvsJour->sum(function ($item) {
            return ($item->duree ?? $item->duree_estimee ?? 90) + 30;
        });

        $limit = LimiteJournaliere::where('user_id', $employeId)
            ->whereDate('date', $date)
            ->value('limite');

        $sameCityBonus = $rdvsJour->contains(fn($item) => filled($rdv->ville) && $item->ville === $rdv->ville) ? -40 : 0;

        $score = $loadMinutes + ($rdvsJour->count() * 25) + $sameCityBonus;

        if ($limit && $rdvsJour->count() >= $limit) {
            $score += 500;
        }

        return [
            'score' => $score,
            'load_minutes' => $loadMinutes,
            'rdv_count' => $rdvsJour->count(),
            'has_conflict' => $hasConflict,
            'same_city_bonus' => $sameCityBonus,
        ];
    }

    public function getPremiumClientsCountProperty(): int
    {
        return \App\Models\User::where('role', 'client')
            ->where('plan_type', 'premium')
            ->where('plan_status', 'active')
            ->count();
    }

    public function getStandardClientsCountProperty(): int
    {
        return \App\Models\User::where('role', 'client')
            ->where('plan_type', 'standard')
            ->count();
    }

    public function getActiveSubscriptionsCountProperty(): int
    {
        return Subscription::where('stripe_status', 'active')->count();
    }

    public function getPremiumClientsProperty()
    {
        return \App\Models\User::with('subscriptions')
            ->where('role', 'client')
            ->where('plan_type', 'premium')
            ->where('plan_status', 'active')
            ->latest()
            ->limit(8)
            ->get();
    }

    public function getPremiumRendezVousProperty()
    {
        return \App\Models\RendezVous::with(['client', 'employe'])
            ->whereHas('client', function ($q) {
                $q->where('plan_type', 'premium')
                    ->where('plan_status', 'active');
            })
            ->orderBy('date')
            ->orderBy('heure')
            ->limit(10)
            ->get();
    }

    public function getRendezVousSansEmployeProperty()
    {
        return \App\Models\RendezVous::with('client')
            ->whereNull('employe_id')
            ->whereIn('status', ['en_attente', 'confirme'])
            ->orderBy('date')
            ->orderBy('heure')
            ->limit(10)
            ->get();
    }

    public function getPremiumClientsWithoutFavoritesProperty()
    {
        return \App\Models\User::where('role', 'client')
            ->where('plan_type', 'premium')
            ->where('plan_status', 'active')
            ->whereDoesntHave('favoriteEmployes')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin-dashboard', [
            'employes' => $this->employes,
            'clients' => $this->clients,
            'stats' => $this->statistiquesData,
            'rdvs' => $this->rdvs,
            'urgences' => $this->urgences,
            'interventionsDuJour' => $this->interventionsDuJour,
            'chargeEmployes' => $this->chargeEmployes,
            'missionsTerminees' => $this->missionsTerminees,
            'qualiteMissions' => $this->qualiteMissions,
            'qualiteStats' => $this->qualiteStats,
            'selectedMission' => $this->selectedMission,
            'recentActivityLogs' => $this->recentActivityLogs,
            'topServices' => $this->topServices,
            'topVilles' => $this->topVilles,
            'dureeStats' => $this->dureeStats,
            'performanceEmployes' => $this->performanceEmployes,
            'feedbackRate' => $this->feedbackRate,
            'recommendations' => $this->recommendations,
            'adminKpis' => $this->adminKpis,
            'urgencesVieillissantes' => $this->urgencesVieillissantes,
            'servicesSousEstimes' => $this->servicesSousEstimes,
            'suggestedEmployees' => $this->suggestedEmployees,
            'premiumClientsCount' => $this->premiumClientsCount,
            'standardClientsCount' => $this->standardClientsCount,
            'activeSubscriptionsCount' => $this->activeSubscriptionsCount,
            'premiumClients' => $this->premiumClients,
            'premiumRendezVous' => $this->premiumRendezVous,
            'rendezVousSansEmploye' => $this->rendezVousSansEmploye,
            'premiumClientsWithoutFavorites' => $this->premiumClientsWithoutFavorites,
        ])->layout('layouts.app');
    }
}
