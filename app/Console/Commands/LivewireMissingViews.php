<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LivewireMissingViews extends Command
{
    protected $signature = 'livewire:missing-views';
    protected $description = 'Liste les composants Livewire sans fichier blade associé';

    public function handle()
    {
        $componentDir = app_path('Livewire');
        $viewDir = resource_path('views/livewire');

        $missing = [];

        foreach (File::allFiles($componentDir) as $file) {
            $filename = $file->getFilenameWithoutExtension(); // Ex: AdminDashboard
            $viewFile = $viewDir . '/' . \Str::kebab($filename) . '.blade.php';

            if (!File::exists($viewFile)) {
                $missing[] = "🔍 $filename → fichier manquant : " . basename($viewFile);
            }
        }

        if (empty($missing)) {
            $this->info('✅ Tous les composants ont leur fichier blade.');
        } else {
            $this->warn("❗ Composants sans blade associé :");
            foreach ($missing as $line) {
                $this->line($line);
            }
        }
    }
}
