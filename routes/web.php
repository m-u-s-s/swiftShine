<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\AdminDashboard;
use App\Livewire\ClientDashboard;
use App\Livewire\EmployeDashboard;
use App\Livewire\Client\PrendreRendezVous;
use App\Livewire\Admin\OutilsAdmin;
use App\Http\Controllers\ExportRendezVousController;
use App\Http\Controllers\FeedbackExportController;
use App\Http\Controllers\FeedbackInviteController;
use App\Models\Feedback;
use App\Models\RendezVous;
use App\Models\User;

Route::get('/', PrendreRendezVous::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            'employe' => redirect()->route('employe.dashboard'),
            default => abort(403),
        };
    })->name('dashboard');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
            Route::get('/outils', OutilsAdmin::class)->name('outils');

            Route::get('/feedbacks/export', [FeedbackExportController::class, 'export'])->name('feedbacks.export');
            Route::get('/feedbacks/export-csv', [FeedbackExportController::class, 'exportCsv'])->name('feedbacks.export.csv');

            Route::get('/export/pdf', function () {
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

                return Pdf::loadView($view, ['data' => $data])
                    ->download($type . '_' . now()->format('Ymd_His') . '.pdf');
            })->name('export.pdf');

            Route::get('/export/{format}/{employeId?}', [ExportRendezVousController::class, 'export'])
                ->name('export.rendezvous');
        });

    Route::prefix('dashboard/client')
        ->name('client.')
        ->middleware('role:client')
        ->group(function () {
            Route::get('/', ClientDashboard::class)->name('dashboard');
        });

    Route::prefix('dashboard/employe')
        ->name('employe.')
        ->middleware('role:employe')
        ->group(function () {
            Route::get('/', EmployeDashboard::class)->name('dashboard');
        });

    Route::prefix('feedback')
        ->name('feedback.')
        ->middleware('role:client')
        ->group(function () {
            Route::get('/ajouter/{rendezVous}', [FeedbackInviteController::class, 'create'])->name('create');
            Route::post('/ajouter/{rendezVous}', [FeedbackInviteController::class, 'store'])->name('store');
        });
});