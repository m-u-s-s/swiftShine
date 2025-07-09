<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feedback;
use App\Models\User;
use App\Models\RendezVous;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::where('role', 'client')->get();
        $employes = User::where('role', 'employe')->get();

        if ($clients->isEmpty() || $employes->isEmpty()) {
            $this->command->warn('⚠️ Pas de clients ou d’employés pour générer les feedbacks.');
            return;
        }

        $rdvs = RendezVous::inRandomOrder()->take(10)->get();

        foreach ($rdvs as $rdv) {
            Feedback::create([
                'client_id' => $rdv->client_id,
                'rendez_vous_id' => $rdv->id,
                'note' => rand(2, 5),
                'commentaire' => fake()->paragraph(1),
                'reponse_admin' => rand(0, 1) ? fake()->sentence() : null,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('✅ FeedbackSeeder exécuté : feedbacks générés.');
    }
}
