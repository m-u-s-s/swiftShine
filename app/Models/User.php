<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tva_number',
        'duree_creneau',
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
    ];

    protected $appends = [
        'profile_photo_url',
        'is_admin',
    ];

    public function disponibilites()
    {
        return $this->hasMany(Disponibilite::class);
    }

    public function rendezVousEmploye()
    {
        return $this->hasMany(RendezVous::class, 'employe_id');
    }

    public function rendezVousClient()
    {
        return $this->hasMany(RendezVous::class, 'client_id');
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
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'client_id');
    }
}
