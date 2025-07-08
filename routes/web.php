
<?php

use App\Livewire\{
    AdminDashboard,
    ClientDashboard,
    EmployeDashboard
};

use App\Livewire\Client\PrendreRendezVous;
use App\Livewire\Client\CalendrierPriseRdv;

Route::get('/', PrendreRendezVous::class)->name('home');

Route::middleware(['auth'])->get('/dashboard', function () {
    return redirect()->route(auth()->user()->role . '.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/admin', AdminDashboard::class)->name('admin.dashboard')->middleware('role:admin');
    Route::get('/dashboard/client', ClientDashboard::class)->name('client.dashboard')->middleware('role:client');
    Route::get('/dashboard/employe', EmployeDashboard::class)->name('employe.dashboard')->middleware('role:employe');
});
