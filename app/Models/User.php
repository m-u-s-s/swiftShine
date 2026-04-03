<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Billable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tva_number',
        'duree_creneau',
        'plan_type',
        'plan_status',
        'premium_started_at',
        'premium_renewal_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'duree_creneau' => 'integer',
        'premium_started_at' => 'datetime',
        'premium_renewal_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
        'is_admin',
    ];

    public function disponibilites(): HasMany
    {
        return $this->hasMany(Disponibilite::class, 'employe_id');
    }

    public function rendezVousEmploye(): HasMany
    {
        return $this->hasMany(RendezVous::class, 'employe_id');
    }

    public function rendezVousClient(): HasMany
    {
        return $this->hasMany(RendezVous::class, 'client_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'client_id');
    }

    public function favoriteEmployes(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'client_employee_preferences',
            'client_id',
            'employe_id'
        )->withPivot('is_favorite')->withTimestamps();
    }

    public function preferredByClients(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'client_employee_preferences',
            'employe_id',
            'client_id'
        )->withPivot('is_favorite')->withTimestamps();
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmploye(): bool
    {
        return $this->role === 'employe';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isPremium(): bool
    {
        return $this->isClient()
            && $this->plan_type === 'premium'
            && $this->plan_status === 'active';
    }

    public function isStandard(): bool
    {
        return $this->isClient() && $this->plan_type === 'standard';
    }

    public function canChooseEmployee(): bool
    {
        return $this->isPremium();
    }

    public function canViewEmployeeAvailability(): bool
    {
        return $this->isPremium();
    }

    public function hasBillingIssue(): bool
    {
        return $this->plan_type === 'premium'
            && $this->plan_status === 'past_due';
    }
}