<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LimiteJournaliere extends Model
{
    protected $fillable = ['user_id', 'date', 'limite'];
    protected $table = 'limites_journalières';


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
