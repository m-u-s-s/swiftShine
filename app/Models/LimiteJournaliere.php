<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LimiteJournaliere extends Model
{
    protected $fillable = ['user_id', 'date', 'limite', 'verrou_admin'];
    protected $table = 'limites_journalieres';


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
