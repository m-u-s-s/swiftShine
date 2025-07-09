<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\RendezVous;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ExportTools extends Component
{
    public $type = 'rendez_vous'; // ou 'utilisateurs', 'feedbacks'
    public $format = 'csv'; // ou 'pdf'

    public function export()
    {
        $data = match ($this->type) {
            'rendez_vous' => RendezVous::with(['client', 'employe'])->get(),
            'utilisateurs' => User::all(),
            'feedbacks' => Feedback::with(['client', 'rendezVous'])->get(),
            default => collect()
        };

        $filename = $this->type . '_' . now()->format('Ymd_His');

        if ($this->format === 'csv') {
            return $this->exportCsv($data, $filename);
        }

        if ($this->format === 'pdf') {
            return redirect()->route('admin.export.pdf', [
                'type' => $this->type
            ]);
        }
    }

    public function exportCsv($data, $filename)
    {
        $csv = '';

        if ($data->isEmpty()) {
            return session()->flash('error', 'Aucune donnée à exporter.');
        }

        $headers = array_keys($data->first()->toArray());
        $csv .= implode(',', $headers) . "\n";

        foreach ($data as $item) {
            $csv .= implode(',', array_map(fn($v) => '"' . Str::of($v)->replace('"', '""') . '"', $item->toArray())) . "\n";
        }

        $path = "exports/{$filename}.csv";
        Storage::put($path, $csv);

        return Response::download(storage_path("app/{$path}"))->deleteFileAfterSend();
    }

    public function render()
    {
        return view('livewire.admin.export-tools');
    }
}

