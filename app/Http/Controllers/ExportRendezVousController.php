<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportRendezVousController extends Controller
{
    public function export($format, $employeId = null)
    {
        $query = RendezVous::with(['client', 'employe']);

        if ($employeId && $employeId !== 'tous') {
            $query->where('employe_id', $employeId);
        }

        $rdvs = $query->orderBy('date')->get();

        if ($format === 'csv') {
            $csv = implode(",", ['Date', 'Heure', 'Client', 'Employé', 'Statut']) . "\n";

            foreach ($rdvs as $r) {
                $csv .= implode(",", [
                    $r->date,
                    $r->heure,
                    $r->client->name ?? '',
                    $r->employe->name ?? '',
                    $r->status
                ]) . "\n";
            }

            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="rendezvous.csv"',
            ]);
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.rendezvous-pdf', compact('rdvs'));
            return $pdf->download('rendezvous.pdf');
        }

        abort(400, 'Format non pris en charge');
    }
}
