<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ImportCsv extends Component
{
    use WithFileUploads;

    public $csv;
    public $type = 'clients';

    public function import()
    {
        Gate::authorize('import', User::class);

        $this->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $path = $this->csv->store('imports');
        $rows = array_map('str_getcsv', file(storage_path('app/' . $path)));

        if (empty($rows)) {
            session()->flash('error', 'Le fichier CSV est vide.');
            return;
        }

        $headers = array_map('strtolower', array_shift($rows));

        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (count($row) !== count($headers)) {
                $skipped++;
                continue;
            }

            $data = array_combine($headers, $row);

            if ($this->type === 'clients') {
                $validator = Validator::make($data, [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                    'role' => 'nullable|in:client,societe',
                ]);

                if ($validator->fails()) {
                    $skipped++;
                    continue;
                }

                User::create([
                    'name' => trim($data['name']),
                    'email' => trim($data['email']),
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'] ?: 'client',
                ]);

                $imported++;
            }

            if ($this->type === 'rendez_vous') {
                $status = match (strtolower(trim($data['status'] ?? 'en_attente'))) {
                    'confirme', 'confirmé' => 'confirme',
                    'refuse' => 'refuse',
                    'en attente', 'en_attente' => 'en_attente',
                    default => 'en_attente',
                };

                $validator = Validator::make(
                    array_merge($data, ['status' => $status]),
                    [
                        'date' => 'required|date',
                        'heure' => 'required',
                        'client_id' => 'required|exists:users,id',
                        'employe_id' => 'required|exists:users,id',
                        'status' => 'required|in:confirme,refuse,en_attente',
                    ]
                );

                if ($validator->fails()) {
                    $skipped++;
                    continue;
                }

                $client = User::find($data['client_id']);
                $employe = User::find($data['employe_id']);

                if (! $client || $client->role !== 'client') {
                    $skipped++;
                    continue;
                }

                if (! $employe || $employe->role !== 'employe') {
                    $skipped++;
                    continue;
                }

                RendezVous::create([
                    'date' => $data['date'],
                    'heure' => $data['heure'],
                    'client_id' => $data['client_id'],
                    'employe_id' => $data['employe_id'],
                    'status' => $status,
                ]);

                $imported++;
            }
        }

        session()->flash('success', "✅ $imported ligne(s) importée(s), $skipped ignorée(s).");
    }

    public function render()
    {
        return view('livewire.admin.import-csv');
    }
}