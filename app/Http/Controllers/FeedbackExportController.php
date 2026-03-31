<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Support\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FeedbackExportController extends Controller
{
    public function export(Request $request)
    {
        Gate::authorize('export', Feedback::class);

        $feedbacks = Feedback::with(['client', 'rendezVous.employe'])
            ->when(
                $request->employe_id,
                fn ($q) => $q->whereHas('rendezVous', fn ($r) => $r->where('employe_id', $request->employe_id))
            )
            ->when(
                $request->client_id,
                fn ($q) => $q->where('client_id', $request->client_id)
            )
            ->when(
                $request->status,
                fn ($q) => $q->whereHas('rendezVous', fn ($r) => $r->where('status', $request->status))
            )
            ->latest()
            ->get();

        ActivityLogger::log('export_feedbacks', null, [
            'format' => 'pdf',
            'employe_id' => $request->employe_id,
            'client_id' => $request->client_id,
            'status' => $request->status,
            'count' => $feedbacks->count(),
        ]);

        $pdf = Pdf::loadView('exports.feedbacks', [
            'data' => $feedbacks,
        ]);

        return $pdf->download('feedbacks_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        Gate::authorize('export', Feedback::class);

        $feedbacks = Feedback::with(['client', 'rendezVous.employe'])
            ->when(
                $request->employe_id,
                fn ($q) => $q->whereHas('rendezVous', fn ($r) => $r->where('employe_id', $request->employe_id))
            )
            ->when(
                $request->client_id,
                fn ($q) => $q->where('client_id', $request->client_id)
            )
            ->when(
                $request->status,
                fn ($q) => $q->whereHas('rendezVous', fn ($r) => $r->where('status', $request->status))
            )
            ->latest()
            ->get();

        ActivityLogger::log('export_feedbacks', null, [
            'format' => 'csv',
            'employe_id' => $request->employe_id,
            'client_id' => $request->client_id,
            'status' => $request->status,
            'count' => $feedbacks->count(),
        ]);

        $filename = 'feedbacks_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($feedbacks) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'id',
                'client',
                'employe',
                'note',
                'commentaire',
                'reponse_admin',
                'created_at',
            ]);

            foreach ($feedbacks as $feedback) {
                fputcsv($file, [
                    $feedback->id,
                    $feedback->client?->name,
                    $feedback->rendezVous?->employe?->name,
                    $feedback->note,
                    $feedback->commentaire,
                    $feedback->reponse_admin,
                    $feedback->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
