<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class FindUnusedLivewireComponents extends Command
{
    protected $signature = 'livewire:unused';
    protected $description = 'Liste les composants Livewire qui ne sont utilisés dans aucune route';

    public function handle()
    {
        $componentDir = app_path('Livewire');

        if (!File::isDirectory($componentDir)) {
            $this->error("❌ Le dossier app/Livewire n'existe pas.");
            return;
        }

        $allFiles = File::allFiles($componentDir);
        $declaredComponents = [];

        foreach ($allFiles as $file) {
            $class = str_replace(
                ['/', '.php'],
                ['\\', ''],
                'App\\Livewire\\' . $file->getRelativePathname()
            );

            if (class_exists($class) && is_subclass_of($class, Component::class)) {
                $declaredComponents[] = $class;
            }
        }

        $usedComponents = [];

        foreach (Route::getRoutes() as $route) {
            $action = $route->getAction('uses');
            if (is_string($action) && in_array($action, $declaredComponents)) {
                $usedComponents[] = $action;
            }
        }

        $unused = array_diff($declaredComponents, $usedComponents);

        if (empty($unused)) {
            $this->info("✅ Aucun composant inutilisé trouvé.");
        } else {
            $this->warn("❗ Composants Livewire non utilisés dans les routes :");
            foreach ($unused as $comp) {
                $this->line("• $comp");
            }
        }
    }
}
