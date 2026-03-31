<?php

namespace App\Console\Commands;

use App\Models\RendezVous;
use App\Models\User;
use App\Notifications\AdminDigestNotification;
use App\Notifications\DemandeFeedbackNotification;
use App\Notifications\EmployeReaffectationSuggestionNotification;
use App\Notifications\RappelRendezVousNotification;
use App\Notifications\UrgenceRendezVousNotification;
use App\Support\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRendezVousReminders extends Command
{
    protected $signature = 'app:send-rendezvous-reminders';
    protected $description = 'Envoie les rappels, relances, suggestions et alertes liés aux rendez-vous';

    public function handle(): int
    {
        $this->send24hReminders();
        $this->send2hReminders();
        $this->sendFeedbackRequests();
        $this->sendUrgentPendingAlerts();
        $this->sendOverrunAlerts();
        $this->sendLowFeedbackRateDigest();
        $this->sendAutoReassignmentSuggestions();

        $this->info('Rappels et relances envoyés.');
        return self::SUCCESS;
    }

    protected function send24hReminders(): void
    {
        $rdvs = RendezVous::with('client')
            ->where('status', 'confirme')
            ->whereDate('date', now()->addDay()->toDateString())
            ->whereNull('rappel_24h_envoye_at')
            ->get();

        foreach ($rdvs as $rdv) {
            if ($rdv->client) {
                $rdv->client->notify(new RappelRendezVousNotification($rdv, '24h'));
                $rdv->update([
                    'rappel_24h_envoye_at' => now(),
                ]);

                ActivityLogger::system('rappel_24h_envoye', $rdv, [
                    'client_id' => $rdv->client_id,
                ]);
            }
        }
    }

    protected function send2hReminders(): void
    {
        $now = now();
        $targetDate = $now->toDateString();
        $targetTime = $now->copy()->addHours(2)->format('H:i');

        $rdvs = RendezVous::with('client')
            ->where('status', 'confirme')
            ->whereDate('date', $targetDate)
            ->whereNull('rappel_2h_envoye_at')
            ->get()
            ->filter(function ($rdv) use ($targetTime) {
                return substr((string) $rdv->heure, 0, 5) === $targetTime;
            });

        foreach ($rdvs as $rdv) {
            if ($rdv->client) {
                $rdv->client->notify(new RappelRendezVousNotification($rdv, '2h'));
                $rdv->update([
                    'rappel_2h_envoye_at' => now(),
                ]);

                ActivityLogger::system('rappel_2h_envoye', $rdv, [
                    'client_id' => $rdv->client_id,
                ]);
            }
        }
    }

    protected function sendFeedbackRequests(): void
    {
        $rdvs = RendezVous::with('client', 'feedback')
            ->where('status', 'termine')
            ->whereNotNull('mission_finished_at')
            ->where('mission_finished_at', '<=', now()->subHours(2))
            ->whereNull('feedback_demande_envoye_at')
            ->get()
            ->filter(fn ($rdv) => ! $rdv->feedback);

        foreach ($rdvs as $rdv) {
            if ($rdv->client) {
                $rdv->client->notify(new DemandeFeedbackNotification($rdv));
                $rdv->update([
                    'feedback_demande_envoye_at' => now(),
                ]);

                ActivityLogger::system('demande_feedback_envoyee', $rdv, [
                    'client_id' => $rdv->client_id,
                ]);
            }
        }
    }

    protected function sendUrgentPendingAlerts(): void
    {
        $rdvs = RendezVous::with('client')
            ->where('status', 'en_attente')
            ->where('priorite', 'urgente')
            ->whereNull('alerte_urgence_envoyee_at')
            ->where('created_at', '<=', now()->subHours(2))
            ->get();

        $admins = User::where('role', 'admin')->get();

        foreach ($rdvs as $rdv) {
            foreach ($admins as $admin) {
                $admin->notify(new UrgenceRendezVousNotification($rdv));
            }

            $rdv->update([
                'alerte_urgence_envoyee_at' => now(),
            ]);

            ActivityLogger::system('alerte_urgence_envoyee', $rdv, [
                'priorite' => $rdv->priorite,
                'status' => $rdv->status,
            ]);
        }
    }

    protected function sendOverrunAlerts(): void
    {
        $admins = User::where('role', 'admin')->get();

        $missions = RendezVous::query()
            ->where('status', 'termine')
            ->whereNotNull('duree_estimee')
            ->whereNotNull('duree_reelle')
            ->where('mission_finished_at', '>=', now()->subDays(7))
            ->get();

        $problematic = $missions
            ->groupBy('service_type')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'avg_gap' => round($items->avg(fn ($rdv) => $rdv->duree_reelle - $rdv->duree_estimee)),
                ];
            })
            ->filter(fn ($row) => $row['count'] >= 3 && $row['avg_gap'] >= 20);

        if ($problematic->isEmpty()) {
            return;
        }

        $items = $problematic->map(function ($row, $service) {
            return ucfirst(str_replace('_', ' ', (string) $service)) . ' : +' . $row['avg_gap'] . ' min en moyenne.';
        })->values()->all();

        foreach ($admins as $admin) {
            $admin->notify(new AdminDigestNotification($items));
        }

        ActivityLogger::system('alerte_depassement_durees', null, [
            'services' => $problematic->toArray(),
        ]);
    }

    protected function sendLowFeedbackRateDigest(): void
    {
        $admins = User::where('role', 'admin')->get();

        $finished = RendezVous::where('status', 'termine')
            ->where('mission_finished_at', '>=', now()->subDays(30))
            ->count();

        if ($finished === 0) {
            return;
        }

        $withFeedback = RendezVous::where('status', 'termine')
            ->where('mission_finished_at', '>=', now()->subDays(30))
            ->whereHas('feedback')
            ->count();

        $rate = round(($withFeedback / $finished) * 100);

        if ($rate >= 40) {
            return;
        }

        $items = [
            'Le taux de feedback sur 30 jours est de ' . $rate . '%.',
            'Envisager une relance renforcée et un CTA plus visible côté client.',
        ];

        foreach ($admins as $admin) {
            $admin->notify(new AdminDigestNotification($items));
        }

        ActivityLogger::system('alerte_taux_feedback_faible', null, [
            'feedback_rate' => $rate,
            'finished_count' => $finished,
            'with_feedback_count' => $withFeedback,
        ]);
    }

    protected function sendAutoReassignmentSuggestions(): void
    {
        $admins = User::where('role', 'admin')->get();

        $today = today()->toDateString();

        $employees = User::where('role', 'employe')->get();

        $loads = $employees->map(function ($employe) use ($today) {
            $rdvs = RendezVous::where('employe_id', $employe->id)
                ->whereDate('date', $today)
                ->whereIn('status', ['en_attente', 'confirme', 'en_route', 'sur_place'])
                ->get();

            $minutes = $rdvs->sum(fn ($rdv) => ($rdv->duree ?? $rdv->duree_estimee ?? 90) + 30);

            return [
                'employe' => $employe,
                'minutes' => $minutes,
                'rdvs' => $rdvs,
            ];
        });

        $surcharged = $loads->filter(fn ($row) => $row['minutes'] >= 480);
        $available = $loads->filter(fn ($row) => $row['minutes'] <= 240)->sortBy('minutes');

        if ($surcharged->isEmpty() || $available->isEmpty()) {
            return;
        }

        foreach ($surcharged as $row) {
            $mission = $row['rdvs']
                ->whereIn('status', ['en_attente', 'confirme'])
                ->sortByDesc(fn ($rdv) => $rdv->priorite === 'urgente' ? 1 : 0)
                ->sortByDesc('duree_estimee')
                ->first();

            if (! $mission) {
                continue;
            }

            $suggested = $available->first();

            if (! $suggested || $suggested['employe']->id === $mission->employe_id) {
                continue;
            }

            foreach ($admins as $admin) {
                $admin->notify(
                    new EmployeReaffectationSuggestionNotification(
                        $mission,
                        $row['employe']->name,
                        $suggested['employe']->name
                    )
                );
            }

            ActivityLogger::system('suggestion_reaffectation_auto', $mission, [
                'employe_surcharge' => $row['employe']->name,
                'employe_suggere' => $suggested['employe']->name,
                'date' => $today,
            ]);
        }
    }
}