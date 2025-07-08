<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LivewireUnusedInViews extends Command
{
    protected $signature = 'livewire:unused-includes';
    protected $description = 'Liste les composants Livewire jamais appelés dans les vues (layouts, includes...)';

    public function handle()
    {
        $componentDir = app_path('Livewire');
        $viewFiles = File::allFiles(resource_path('views'));

        $used = [];
        foreach ($viewFiles as $view) {
            $content = file_get_contents($view);
            preg_match_all('/@livewire\(\'([a-zA-Z0-9\-_\/]+)\'/', $content, $matches);
            $used = array_merge($used, $matches[1]);
        }

        $used = array_unique($used);
        $used = array_map(fn($s) => \Str::studly(str_replace(['-', '/'], '', $s)), $used);

        $unused = [];

        foreach (File::allFiles($componentDir) as $file) {
            $filename = $file->getFilenameWithoutExtension(); // ex: CalendrierEmploye
            if (!in_array($filename, $used)) {
                $unused[] = $filename;
            }
        }

        if (empty($unused)) {
            $this->info('✅ Tous les composants sont utilisés dans des vues.');
        } else {
            $this->warn("❗ Composants Livewire non inclus dans les vues :");
            foreach ($unused as $name) {
                $this->line("• $name");
            }
        }
    }
}
