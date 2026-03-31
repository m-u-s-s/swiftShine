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
        'status',
        'service_type',
        'adresse',
        'ville',
        'code_postal',
        'type_lieu',
        'surface',
        'frequence',
        'is_recurrent',
        'recurrence_rule',
        'is_favorite_slot',
        'commentaire_client',
        'telephone_client',
        'presence_animaux',
        'acces_parking',
        'materiel_fournit',
        'priorite',
        'photos_reference',
        'options_prestation',
        'zones_specifiques',
        'materiel_specifique',
        'commentaire_fin_mission',
        'duree_reelle',
        'photos_apres',
        'mission_started_at',
        'mission_finished_at',
        'rappel_24h_envoye_at',
        'rappel_2h_envoye_at',
        'feedback_demande_envoye_at',
        'alerte_urgence_envoyee_at',
        'duree_estimee',
        'devis_estime',
    ];

    protected $casts = [
        'date' => 'date',
        'duree' => 'integer',
        'duree_estimee' => 'integer',
        'duree_reelle' => 'integer',
        'devis_estime' => 'decimal:2',
        'presence_animaux' => 'boolean',
        'acces_parking' => 'boolean',
        'materiel_fournit' => 'boolean',
        'is_recurrent' => 'boolean',
        'is_favorite_slot' => 'boolean',
        'photos_reference' => 'array',
        'options_prestation' => 'array',
        'zones_specifiques' => 'array',
        'materiel_specifique' => 'array',
        'photos_apres' => 'array',
        'mission_started_at' => 'datetime',
        'mission_finished_at' => 'datetime',
        'rappel_24h_envoye_at' => 'datetime',
        'rappel_2h_envoye_at' => 'datetime',
        'feedback_demande_envoye_at' => 'datetime',
        'alerte_urgence_envoyee_at' => 'datetime',
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
        return $this->hasOne(Feedback::class, 'rendez_vous_id');
    }

    public function resetNotificationTrackingIfNeeded(array $original = []): void
    {
        $dateChanged = array_key_exists('date', $original) && $original['date'] != $this->date;
        $heureChanged = array_key_exists('heure', $original) && $original['heure'] != $this->heure;
        $statusChanged = array_key_exists('status', $original) && $original['status'] !== $this->status;
        $prioriteChanged = array_key_exists('priorite', $original) && $original['priorite'] !== $this->priorite;

        if ($dateChanged || $heureChanged) {
            $this->rappel_24h_envoye_at = null;
            $this->rappel_2h_envoye_at = null;
        }

        if ($statusChanged && $this->status === 'en_attente') {
            $this->rappel_24h_envoye_at = null;
            $this->rappel_2h_envoye_at = null;
        }

        if ($prioriteChanged && $this->priorite === 'urgente') {
            $this->alerte_urgence_envoyee_at = null;
        }

        if (($dateChanged || $heureChanged) && $this->priorite === 'urgente') {
            $this->alerte_urgence_envoyee_at = null;
        }
    }

    public function isFinalStatus(): bool
    {
        return in_array($this->status, ['refuse', 'termine']);
    }
}