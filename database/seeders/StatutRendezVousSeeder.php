<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RendezVous;

class StatutRendezVousSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['valide', 'refuse', 'en_attente'];

        RendezVous::all()->each(function ($rdv) use ($statuses) {
            $rdv->update([
                'status' => $statuses[array_rand($statuses)],
            ]);
        });

        $this->command->info('✅ Les status des rendez-vous ont été répartis aléatoirement.');
    }
}
