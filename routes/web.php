
<?php

use App\Livewire\{
    AdminDashboard,
    ClientDashboard,
    EmployeDashboard
};

use App\Livewire\Client\PrendreRendezVous;
use App\Livewire\Client\CalendrierPriseRdv;
use App\Http\Controllers\ExportRendezVousController;
use App\Http\Controllers\FeedbackExportController;
use App\Livewire\Admin\OutilsAdmin;
use App\Models\Feedback;
use App\Models\RendezVous;
use App\Models\User;

Route::get('/', PrendreRendezVous::class)->name('home');

Route::middleware(['auth'])->get('/dashboard', function () {
    return redirect()->route(auth()->user()->role . '.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/admin', AdminDashboard::class)->name('admin.dashboard')->middleware('role:admin');
    Route::get('/dashboard/client', ClientDashboard::class)->name('client.dashboard')->middleware('role:client');
    Route::get('/dashboard/employe', EmployeDashboard::class)->name('employe.dashboard')->middleware('role:employe');
});

Route::get('export/{format}/{employeId?}', [ExportRendezVousController::class, 'export'])->name('export.rendezvous');

Route::get('/admin/feedbacks/export', [FeedbackExportController::class, 'export'])
    ->middleware(['auth', 'verified'])
    ->name('admin.feedbacks.export');

Route::get('/admin/feedbacks/export-csv', [FeedbackExportController::class, 'exportCsv'])
    ->middleware(['auth', 'verified'])
    ->name('admin.feedbacks.export.csv');


Route::get('/feedback/ajouter/{rendezVous}', [FeedbackInviteController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('client.feedback.create');

Route::post('/feedback/ajouter/{rendezVous}', [FeedbackInviteController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('client.feedback.store');


Route::get('/admin/outils', OutilsAdmin::class)
    ->middleware(['auth', 'verified', 'admin']) // ou CheckRole::class
    ->name('admin.outils');


Route::get('/admin/export/pdf', function () {
    $type = request('type');

    $view = match ($type) {
        'utilisateurs' => 'exports.users',
        'feedbacks' => 'exports.feedbacks',
        default => 'exports.rendezvous',
    };

    $data = match ($type) {
        'utilisateurs' => User::all(),
        'feedbacks' => Feedback::with(['client', 'rendezVous'])->get(),
        default => RendezVous::with(['client', 'employe'])->get(),
    };

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, ['data' => $data]);
    return $pdf->download($type . '_' . now()->format('Ymd_His') . '.pdf');
})->name('admin.export.pdf');
