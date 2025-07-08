<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;
    protected $table = 'rendez_vous';
    protected $fillable = [
        'client_id',
        'employe_id',
        'date',
        'heure',
        'duree',
        'motif',
        'status'
    ];


    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
}
