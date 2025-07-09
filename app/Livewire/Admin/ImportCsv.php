<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ImportCsv extends Component
{
    use WithFileUploads;

    public $csv;
    public $type = 'clients';

    public function import()
    {
        $this->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $path = $this->csv->store('imports');
        $rows = array_map('str_getcsv', file(storage_path('app/' . $path)));
        $headers = array_map('strtolower', array_shift($rows));

        $imported = 0;

        foreach ($rows as $row) {
            $data = array_combine($headers, $row);

            if ($this->type === 'clients') {
                $validator = Validator::make($data, [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                    'role' => 'in:client,societe',
                ]);

                if ($validator->fails()) continue;

                User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'] ?? 'client',
                ]);
            }

            if ($this->type === 'rendez_vous') {
                $validator = Validator::make($data, [
                    'date' => 'required|date',
                    'heure' => 'required',
                    'client_id' => 'required|exists:users,id',
                    'employe_id' => 'required|exists:users,id',
                    'statut' => 'in:validé,refusé,en attente',
                ]);

                if ($validator->fails()) continue;

                RendezVous::create([
                    'date' => $data['date'],
                    'heure' => $data['heure'],
                    'client_id' => $data['client_id'],
                    'employe_id' => $data['employe_id'],
                    'statut' => $data['statut'],
                ]);
            }

            $imported++;
        }

        session()->flash('success', "✅ $imported ligne(s) importée(s) avec succès.");
    }

    public function render()
    {
        return view('livewire.admin.import-csv');
    }
}
