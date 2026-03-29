<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExportRendezVousController extends Controller
{
    public function export(Request $request, $format, $employeId = null)
    {
        Gate::authorize('export', User::class);

        $rdvs = RendezVous::with(['client', 'employe'])
            ->when($employeId, fn($q) =>
                $q->where('employe_id', $employeId)
            )
            ->orderBy('date')
            ->orderBy('heure')
            ->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.rendezvous', [
                'data' => $rdvs
            ]);

            return $pdf->download('rendezvous_' . now()->format('Ymd_His') . '.pdf');
        }

        if ($format === 'csv') {
            $filename = 'rendezvous_' . now()->format('Ymd_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ];

            $callback = function () use ($rdvs) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'id',
                    'client',
                    'employe',
                    'date',
                    'heure',
                    'status',
                    'created_at',
                ]);

                foreach ($rdvs as $rdv) {
                    fputcsv($file, [
                        $rdv->id,
                        $rdv->client?->name,
                        $rdv->employe?->name,
                        $rdv->date,
                        $rdv->heure,
                        $rdv->status,
                        $rdv->created_at,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        abort(404);
    }
}