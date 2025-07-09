<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Barryvdh\DomPDF\Facade\Pdf;

class FeedbackExportController extends Controller
{
    public function export(Request $request)
    {
        $query = Feedback::with('client', 'rendezVous.employe');

        if ($request->filled('employe_id')) {
            $query->whereHas('rendezVous', fn($q) =>
                $q->where('employe_id', $request->employe_id));
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $feedbacks = $query->get();

        $pdf = Pdf::loadView('exports.feedbacks-pdf', compact('feedbacks'));
        return $pdf->download('feedbacks.pdf');
    }
    public function exportCsv(Request $request)
{
    $query = Feedback::with('client', 'rendezVous.employe');

    if ($request->filled('employe_id')) {
        $query->whereHas('rendezVous', fn($q) =>
            $q->where('employe_id', $request->employe_id));
    }

    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }

    $feedbacks = $query->get();

    $csv = fopen('php://output', 'w');
    $filename = 'feedbacks_export.csv';

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    // En-têtes CSV
    fputcsv($csv, ['Client', 'Employé', 'Note', 'Commentaire', 'Date']);

    foreach ($feedbacks as $f) {
        fputcsv($csv, [
            $f->client->name,
            $f->rendezVous->employe->name ?? '—',
            $f->note,
            $f->commentaire,
            $f->created_at->format('Y-m-d'),
        ]);
    }

    fclose($csv);
    exit;
}

}
