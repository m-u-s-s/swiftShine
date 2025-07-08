<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feedback;
use App\Models\RendezVous;
use App\Models\User;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        // On sélectionne un client avec au moins 1 rendez-vous passé
        $client = User::where('role', 'client')->first();

        if (!$client) {
            $this->command->warn("⚠️ Aucun client trouvé pour créer un feedback.");
            return;
        }

        $rdv = RendezVous::where('client_id', $client->id)
            ->whereDate('date', '<', now())
            ->first();

        if (!$rdv) {
            $this->command->warn("⚠️ Aucun RDV passé trouvé pour ce client.");
            return;
        }

        Feedback::create([
            'rendez_vous_id' => $rdv->id,
            'client_id' => $client->id,
            'commentaire' => 'Service impeccable, merci beaucoup !',
            'note' => 5,
        ]);

        $this->command->info("✅ Feedback inséré avec succès.");
    }
}
