<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Feedback;
use App\Models\RendezVous;
use App\Models\User;
use App\Policies\FeedbackPolicy;
use App\Policies\RendezVousPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        RendezVous::class => RendezVousPolicy::class,
        Feedback::class => FeedbackPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
