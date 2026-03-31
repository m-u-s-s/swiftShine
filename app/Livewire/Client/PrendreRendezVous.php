<?php

namespace App\Livewire\Client;

use App\Models\RendezVous;
use App\Models\User;
use App\Notifications\NouveauRendezVousNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class PrendreRendezVous extends Component
{
    use WithFileUploads;

    public $step = 1;

    public $employe_id;
    public $rdvDate;
    public $rdvHeure;
    public $rdvEnvoye = false;

    public $service_type;
    public $adresse;
    public $ville;
    public $code_postal;
    public $type_lieu;
    public $surface;
    public $frequence;
    public $commentaire_client;

    public $telephone_client;
    public $presence_animaux = false;
    public $acces_parking = false;
    public $materiel_fournit = false;
    public $priorite;
    public $photos = [];
    public $photos_reference = [];
    public $duree_estimee = null;
    public $devis_estime = null;

    public $options_prestation = [];
    public $zones_specifiques = [];
    public $materiel_specifique = [];
    public $is_recurrent = false;
    public $recurrence_rule = null;
    public $is_favorite_slot = false;

    public function getEmployesProperty()
    {
        return User::select('id', 'name')
            ->where('role', 'employe')
            ->get();
    }

    public function getEmployeNomProperty()
    {
        return User::find($this->employe_id)?->name;
    }

    public function getServicesProperty()
    {
        return [
            'nettoyage_maison' => 'Nettoyage maison',
            'nettoyage_appartement' => 'Nettoyage appartement',
            'nettoyage_bureau' => 'Nettoyage bureau',
            'nettoyage_vitres' => 'Nettoyage vitres',
            'nettoyage_fin_chantier' => 'Nettoyage fin de chantier',
            'nettoyage_apres_demenagement' => 'Nettoyage après déménagement',
            'nettoyage_profond' => 'Nettoyage en profondeur',
        ];
    }

    public function getTypesLieuxProperty()
    {
        return [
            'maison' => 'Maison',
            'appartement' => 'Appartement',
            'bureau' => 'Bureau',
            'commerce' => 'Commerce',
            'autre' => 'Autre',
        ];
    }

    public function getFrequencesProperty()
    {
        return [
            'une_fois' => 'Une seule fois',
            'hebdomadaire' => 'Hebdomadaire',
            'bi_hebdomadaire' => 'Toutes les 2 semaines',
            'mensuelle' => 'Mensuelle',
        ];
    }

    public function getPrioritesProperty()
    {
        return [
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente',
        ];
    }

    public function getOptionsPrestationsDisponiblesProperty()
    {
        return [
            'vitres' => 'Vitres',
            'four' => 'Four',
            'frigo' => 'Frigo',
            'repassage' => 'Repassage',
            'sanitaires' => 'Sanitaires renforcés',
            'poussiere_detail' => 'Poussière détaillée',
        ];
    }

    public function getZonesDisponiblesProperty()
    {
        return [
            'cuisine' => 'Cuisine',
            'salon' => 'Salon',
            'salle_de_bain' => 'Salle de bain',
            'chambres' => 'Chambres',
            'escaliers' => 'Escaliers',
            'terrasse' => 'Terrasse',
            'garage' => 'Garage',
        ];
    }

    public function getMaterielsDisponiblesProperty()
    {
        return [
            'produits_eco' => 'Produits écologiques',
            'aspirateur_industriel' => 'Aspirateur industriel',
            'produit_anti_calcaire' => 'Anti-calcaire',
            'materiel_vitres' => 'Matériel vitres',
            'desinfectant' => 'Désinfectant',
        ];
    }

    #[On('creneauChoisi')]
    public function setCreneau($date, $heure)
    {
        $this->rdvDate = $date;
        $this->rdvHeure = $heure;
    }

    public function updatedPhotos()
    {
        $this->validate([
            'photos.*' => ['image', 'max:4096'],
        ]);
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'service_type' => ['required', 'string'],
                'type_lieu' => ['required', 'string'],
                'frequence' => ['required', 'string'],
                'surface' => ['required', 'string', 'max:255'],
            ]);

            $this->duree_estimee = $this->calculerDureeEstimee();
            $this->devis_estime = $this->calculerDevisEstime();
        }

        if ($this->step === 2) {
            $this->validate([
                'adresse' => ['required', 'string', 'max:255'],
                'ville' => ['required', 'string', 'max:255'],
                'code_postal' => ['required', 'string', 'max:20'],
                'telephone_client' => ['required', 'string', 'max:30'],
                'priorite' => ['required', 'string'],
                'commentaire_client' => ['nullable', 'string', 'max:1000'],
                'photos.*' => ['nullable', 'image', 'max:4096'],
            ]);
        }

        if ($this->step === 3 && !$this->employe_id) {
            $this->addError('employe_id', 'Veuillez choisir un employé.');
            return;
        }

        if ($this->step === 4 && (!$this->rdvDate || !$this->rdvHeure)) {
            $this->addError('rdvDate', 'Veuillez choisir une date et une heure.');
            return;
        }

        if ($this->step < 5) {
            $this->step++;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function removePhoto($index)
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);
        }
    }

    public function validerRdv()
    {
        if (!Auth::check()) {
            $this->addError('rdvDate', 'Vous devez être connecté pour réserver un rendez-vous.');
            return;
        }

        Gate::authorize('create', RendezVous::class);

        $this->validate([
            'service_type' => ['required', 'string'],
            'type_lieu' => ['required', 'string'],
            'frequence' => ['required', 'string'],
            'surface' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:20'],
            'telephone_client' => ['required', 'string', 'max:30'],
            'priorite' => ['required', 'string'],
            'commentaire_client' => ['nullable', 'string', 'max:1000'],
            'employe_id' => ['required', 'exists:users,id'],
            'rdvDate' => ['required', 'date'],
            'rdvHeure' => ['required'],
            'photos.*' => ['nullable', 'image', 'max:4096'],
            'duree_estimee' => ['nullable', 'integer', 'min:30'],
            'devis_estime' => ['nullable', 'numeric', 'min:0'],
        ]);

        $existe = RendezVous::where('employe_id', $this->employe_id)
            ->where('date', $this->rdvDate)
            ->where('heure', $this->rdvHeure)
            ->whereIn('status', ['confirme', 'en_attente', 'en_route', 'sur_place'])
            ->exists();

        if ($existe) {
            $this->addError('rdvDate', 'Ce créneau vient d’être réservé. Veuillez en choisir un autre.');
            return;
        }

        $storedPhotos = [];

        foreach ($this->photos as $photo) {
            $storedPhotos[] = $photo->store('rendezvous/photos', 'public');
        }

        $rdv = RendezVous::create([
            'client_id' => Auth::id(),
            'employe_id' => $this->employe_id,
            'date' => $this->rdvDate,
            'heure' => $this->rdvHeure,
            'status' => 'en_attente',
            'service_type' => $this->service_type,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'type_lieu' => $this->type_lieu,
            'surface' => $this->surface,
            'frequence' => $this->frequence,
            'is_recurrent' => $this->is_recurrent,
            'recurrence_rule' => $this->is_recurrent ? $this->recurrence_rule : null,
            'is_favorite_slot' => $this->is_favorite_slot,
            'commentaire_client' => $this->commentaire_client,
            'telephone_client' => $this->telephone_client,
            'presence_animaux' => $this->presence_animaux,
            'acces_parking' => $this->acces_parking,
            'materiel_fournit' => $this->materiel_fournit,
            'priorite' => $this->priorite,
            'photos_reference' => $storedPhotos,
            'options_prestation' => $this->options_prestation,
            'zones_specifiques' => $this->zones_specifiques,
            'materiel_specifique' => $this->materiel_specifique,
            'duree_estimee' => $this->duree_estimee,
            'devis_estime' => $this->devis_estime,
        ]);

        $rdv->load('client', 'employe');

        if ($rdv->employe) {
            $rdv->employe->notify(new NouveauRendezVousNotification($rdv));
        }

        $this->reset([
            'step',
            'employe_id',
            'rdvDate',
            'rdvHeure',
            'service_type',
            'adresse',
            'ville',
            'code_postal',
            'type_lieu',
            'surface',
            'frequence',
            'commentaire_client',
            'telephone_client',
            'presence_animaux',
            'acces_parking',
            'materiel_fournit',
            'priorite',
            'photos',
            'photos_reference',
            'duree_estimee',
            'devis_estime',
            'options_prestation',
            'zones_specifiques',
            'materiel_specifique',
            'is_recurrent',
            'recurrence_rule',
            'is_favorite_slot',
        ]);

        $this->step = 1;
        $this->rdvEnvoye = true;

        $this->dispatch('toast', 'Votre demande de nettoyage a été enregistrée avec succès.', 'success');
    }

    public function updatedServiceType()
    {
        $this->refreshEstimations();
    }

    public function updatedTypeLieu()
    {
        $this->refreshEstimations();
    }

    public function updatedFrequence()
    {
        $this->refreshEstimations();
    }

    public function updatedSurface()
    {
        $this->refreshEstimations();
    }

    public function updatedPriorite()
    {
        $this->refreshEstimations();
    }

    public function updatedOptionsPrestation()
    {
        $this->refreshEstimations();
    }

    public function updatedZonesSpecifiques()
    {
        $this->refreshEstimations();
    }

    public function updatedMaterielSpecifique()
    {
        $this->refreshEstimations();
    }

    protected function refreshEstimations(): void
    {
        $this->duree_estimee = $this->calculerDureeEstimee();
        $this->devis_estime = $this->calculerDevisEstime();
    }

    public function calculerDureeEstimee(): ?int
    {
        if (!$this->service_type || !$this->surface) {
            return null;
        }

        $base = match ($this->service_type) {
            'nettoyage_maison' => 120,
            'nettoyage_appartement' => 90,
            'nettoyage_bureau' => 120,
            'nettoyage_vitres' => 60,
            'nettoyage_fin_chantier' => 240,
            'nettoyage_apres_demenagement' => 180,
            'nettoyage_profond' => 210,
            default => 90,
        };

        $surfaceText = strtolower(trim((string) $this->surface));
        $surfaceMinutes = 0;

        if (preg_match('/(\d+)/', $surfaceText, $matches)) {
            $surfaceValue = (int) $matches[1];

            if ($surfaceValue <= 50) {
                $surfaceMinutes = 0;
            } elseif ($surfaceValue <= 100) {
                $surfaceMinutes = 30;
            } elseif ($surfaceValue <= 150) {
                $surfaceMinutes = 60;
            } elseif ($surfaceValue <= 250) {
                $surfaceMinutes = 90;
            } else {
                $surfaceMinutes = 120;
            }
        }

        $lieuMinutes = match ($this->type_lieu) {
            'maison' => 20,
            'bureau' => 30,
            'commerce' => 25,
            default => 0,
        };

        $frequenceAdjustment = match ($this->frequence) {
            'hebdomadaire' => -15,
            'bi_hebdomadaire' => 0,
            'mensuelle' => 15,
            'une_fois' => 20,
            default => 0,
        };

        $prioriteAdjustment = match ($this->priorite) {
            'urgente' => 15,
            'haute' => 10,
            default => 0,
        };

        $animauxAdjustment = $this->presence_animaux ? 10 : 0;
        $optionsAdjustment = count($this->options_prestation ?? []) * 15;
        $zonesAdjustment = count($this->zones_specifiques ?? []) * 10;
        $materielAdjustment = count($this->materiel_specifique ?? []) * 5;

        $total = $base
            + $surfaceMinutes
            + $lieuMinutes
            + $frequenceAdjustment
            + $prioriteAdjustment
            + $animauxAdjustment
            + $optionsAdjustment
            + $zonesAdjustment
            + $materielAdjustment;

        return max($total, 30);
    }

    public function calculerDevisEstime(): ?float
    {
        if (!$this->duree_estimee) {
            return null;
        }

        $baseRate = match ($this->service_type) {
            'nettoyage_vitres' => 32,
            'nettoyage_fin_chantier' => 42,
            'nettoyage_apres_demenagement' => 38,
            'nettoyage_profond' => 40,
            'nettoyage_bureau' => 35,
            default => 30,
        };

        $hours = $this->duree_estimee / 60;
        $amount = $hours * $baseRate;

        $amount += count($this->options_prestation ?? []) * 12;
        $amount += count($this->zones_specifiques ?? []) * 6;
        $amount += $this->priorite === 'urgente' ? 25 : 0;

        return round($amount, 2);
    }

    public function render()
    {
        return view('livewire.client.prendre-rendez-vous', [
            'employes' => $this->employes,
            'employeNom' => $this->employeNom,
            'services' => $this->services,
            'typesLieux' => $this->typesLieux,
            'frequences' => $this->frequences,
            'priorites' => $this->priorites,
            'optionsPrestationsDisponibles' => $this->optionsPrestationsDisponibles,
            'zonesDisponibles' => $this->zonesDisponibles,
            'materielsDisponibles' => $this->materielsDisponibles,
        ])->layout('layouts.app');
    }
}