<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LimiteJournaliere;
use Illuminate\Support\Carbon;

class LimitesJournaliereSeeder extends Seeder
{
    public function run(): void
    {
        $employes = User::where('role', 'employe')->get();
        $jours = now()->startOfWeek();

        foreach ($employes as $emp) {
            foreach (range(0, 6) as $i) {
                LimiteJournaliere::updateOrCreate([
                    'user_id' => $emp->id,
                    'date' => $jours->copy()->addDays($i)->toDateString(),
                ], [
                    'limite' => rand(2, 4),
                    'verrou_admin' => false,
                ]);
            }
        }
    }
}
