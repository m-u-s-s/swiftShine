<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\User;
use App\Support\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExportRendezVousController extends Controller
{
    public function export(Request $request, $format, $employeId = null)
    {
        Gate::authorize('export', User::class);

        $rdvs = RendezVous::with(['client', 'employe'])
            ->when($employeId, fn ($q) => $q->where('employe_id', $employeId))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->ville, fn ($q) => $q->where('ville', $request->ville))
            ->when($request->service_type, fn ($q) => $q->where('service_type', $request->service_type))
            ->when($request->date_from, fn ($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('date', '<=', $request->date_to))
            ->orderBy('date')
            ->orderBy('heure')
            ->get();

        ActivityLogger::log('export_rendez_vous', null, [
            'format' => $format,
            'employe_id' => $employeId,
            'status' => $request->status,
            'ville' => $request->ville,
            'service_type' => $request->service_type,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'count' => $rdvs->count(),
        ]);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.rendezvous', [
                'data' => $rdvs,
            ]);

            return $pdf->download('rendezvous_' . now()->format('Ymd_His') . '.pdf');
        }

        if ($format === 'csv') {
            $filename = 'rendezvous_' . now()->format('Ymd_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($rdvs) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'id',
                    'client',
                    'employe',
                    'service_type',
                    'ville',
                    'date',
                    'heure',
                    'status',
                    'priorite',
                    'created_at',
                ]);

                foreach ($rdvs as $rdv) {
                    fputcsv($file, [
                        $rdv->id,
                        $rdv->client?->name,
                        $rdv->employe?->name,
                        $rdv->service_type,
                        $rdv->ville,
                        $rdv->date,
                        $rdv->heure,
                        $rdv->status,
                        $rdv->priorite,
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