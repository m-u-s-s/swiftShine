<?php

use App\Http\Controllers\ExportRendezVousController;
use App\Http\Controllers\FeedbackExportController;
use App\Http\Controllers\FeedbackInviteController;
use App\Http\Controllers\PremiumCheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Livewire\Admin\AdminPremiumClients;
use App\Livewire\Admin\MissionsAdmin;
use App\Livewire\Admin\OutilsAdmin;
use App\Livewire\Admin\PlanningAdmin;
use App\Livewire\Admin\UtilisateursAdmin;
use App\Livewire\AdminDashboard;
use App\Livewire\AdminFeedbacks;
use App\Livewire\Client\FavoriteEmployesManager;
use App\Livewire\Client\HistoriqueClient;
use App\Livewire\Client\MesRendezVousClient;
use App\Livewire\Client\PremiumOfferPage;
use App\Livewire\Client\PrendreRendezVous;
use App\Livewire\Client\ProfilClient;
use App\Livewire\ClientDashboard;
use App\Livewire\Employe\HistoriqueEmploye;
use App\Livewire\Employe\MissionsEmploye;
use App\Livewire\EmployeDashboard;
use App\Models\Feedback;
use App\Models\RendezVous;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/premium', PremiumOfferPage::class)->name('premium.offer');

/*
|--------------------------------------------------------------------------
| Webhook Stripe
|--------------------------------------------------------------------------
*/
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook');

/*
|--------------------------------------------------------------------------
| Zones protégées
|--------------------------------------------------------------------------
*/
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

    /*
    |--------------------------------------------------------------------------
    | Premium checkout
    |--------------------------------------------------------------------------
    */
    Route::post('/premium/checkout', [PremiumCheckoutController::class, 'checkout'])->name('premium.checkout');
    Route::get('/premium/success', [PremiumCheckoutController::class, 'success'])->name('premium.success');
    Route::get('/premium/cancel', [PremiumCheckoutController::class, 'cancel'])->name('premium.cancel');

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('planning', PlanningAdmin::class)->name('planning');
        Route::get('missions', MissionsAdmin::class)->name('missions');
        Route::get('utilisateurs', UtilisateursAdmin::class)->name('utilisateurs');
        Route::get('feedbacks', AdminFeedbacks::class)->name('feedbacks');
        Route::get('outils', OutilsAdmin::class)->name('outils');
        Route::get('premium-clients', AdminPremiumClients::class)->name('premium.clients');

        Route::get('feedbacks/export', [FeedbackExportController::class, 'export'])->name('feedbacks.export');
        Route::get('feedbacks/export-csv', [FeedbackExportController::class, 'exportCsv'])->name('feedbacks.export.csv');

        Route::get('export/pdf', function () {
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
                ->download($type.'_'.now()->format('Ymd_His').'.pdf');
        })->name('export.pdf');

        Route::get('export/{format}/{employeId?}', [ExportRendezVousController::class, 'export'])
            ->name('export.rendezvous');
    });

    /*
    |--------------------------------------------------------------------------
    | Client
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:client')->prefix('dashboard/client')->name('client.')->group(function () {
        Route::get('/', ClientDashboard::class)->name('dashboard');
        Route::get('rendez-vous/nouveau', PrendreRendezVous::class)->name('rendezvous.create');
        Route::get('rendez-vous', MesRendezVousClient::class)->name('rendezvous.index');
        Route::get('historique', HistoriqueClient::class)->name('historique');
        Route::get('profil', ProfilClient::class)->name('profile');
        Route::get('favoris-employes', FavoriteEmployesManager::class)->name('favorite-employes');
    });

    /*
    |--------------------------------------------------------------------------
    | Employé
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:employe')->prefix('dashboard/employe')->name('employe.')->group(function () {
        Route::get('/', EmployeDashboard::class)->name('dashboard');
        Route::get('missions', MissionsEmploye::class)->name('missions');
        Route::get('historique', HistoriqueEmploye::class)->name('historique');
    });

    /*
    |--------------------------------------------------------------------------
    | Feedback
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:client')->prefix('feedback')->name('feedback.')->group(function () {
        Route::get('ajouter/{rendezVous}', [FeedbackInviteController::class, 'create'])->name('create');
        Route::post('ajouter/{rendezVous}', [FeedbackInviteController::class, 'store'])->name('store');
    });
});