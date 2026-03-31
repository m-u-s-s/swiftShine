<?php

namespace App\Providers;

use App\Models\Feedback;
use App\Models\RendezVous;
use App\Models\User;
use App\Policies\FeedbackPolicy;
use App\Policies\RendezVousPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        RendezVous::class => RendezVousPolicy::class,
        Feedback::class => FeedbackPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        Gate::define('access-admin', fn (User $user) => $user->role === 'admin');
        Gate::define('access-client', fn (User $user) => $user->role === 'client');
        Gate::define('access-employe', fn (User $user) => $user->role === 'employe');
    }
}