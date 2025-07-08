<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\RendezVous;

class ExportRendezVousTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_csv_and_pdf_routes(): void
    {
        RendezVous::factory()->count(3)->create();

        $csvResponse = $this->get('/export/csv');
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Type', 'text/csv');

        $pdfResponse = $this->get('/export/pdf');
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');
    }
}