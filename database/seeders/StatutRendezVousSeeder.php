<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RendezVous;

class StatutRendezVousSeeder extends Seeder
{
    public function run(): void
    {
        $statuts = ['validé', 'refusé', 'en attente'];

        RendezVous::all()->each(function ($rdv) use ($statuts) {
            $rdv->update([
                'statut' => $statuts[array_rand($statuts)],
            ]);
        });

        $this->command->info('✅ Les statuts des rendez-vous ont été répartis aléatoirement.');
    }
}
