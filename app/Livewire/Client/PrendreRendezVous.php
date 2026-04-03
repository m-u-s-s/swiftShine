<?php

namespace App\Livewire\Client;

use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class PrendreRendezVous extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public ?string $service_type = null;
    public ?string $type_lieu = null;
    public ?string $frequence = null;
    public ?string $surface = null;

    public array $options_prestation = [];
    public array $zones_specifiques = [];

    public ?string $materiel_specifique = null;
    public ?string $commentaire_client = null;

    public bool $presence_animaux = false;
    public bool $acces_parking = false;
    public bool $materiel_fournit = false;

    public ?string $adresse = null;
    public ?string $ville = null;
    public ?string $code_postal = null;
    public ?string $telephone_client = null;
    public ?string $priorite = 'normale';

    public ?int $employe_id = null;
    public ?string $rdvDate = null;
    public ?string $rdvHeure = null;

    public bool $is_recurrent = false;
    public ?string $recurrence_rule = null;
    public bool $is_favorite_slot = false;

    public array $photos = [];
    public array $creneauxDisponibles = [];
    public array $employesDisponibles = [];

    public int $duree_estimee = 0;
    public float $devis_estime = 0;

    public function mount(): void
    {
        $this->chargerEmployesDisponibles();
        $this->refreshEstimations();
    }

    public function isPremiumClient(): bool
    {
        return Auth::check() && Auth::user()->canChooseEmployee();
    }

    public function getSurfacesProperty(): array
    {
        return [
            'moins_50' => 'Moins de 50 m²',
            '50_100' => '50 à 100 m²',
            '100_150' => '100 à 150 m²',
            '150_250' => '150 à 250 m²',
            'plus_250' => 'Plus de 250 m²',
        ];
    }

    public function getServicesProperty(): array
    {
        return [
            'nettoyage_standard' => 'Nettoyage standard',
            'nettoyage_profond' => 'Nettoyage en profondeur',
            'fin_de_chantier' => 'Nettoyage fin de chantier',
            'fin_de_bail' => 'Nettoyage fin de bail',
            'bureaux' => 'Nettoyage bureaux / professionnels',
        ];
    }

    public function getTypesLieuxProperty(): array
    {
        return [
            'appartement' => 'Appartement',
            'maison' => 'Maison',
            'bureau' => 'Bureau',
            'commerce' => 'Commerce',
            'autre' => 'Autre',
        ];
    }

    public function getFrequencesProperty(): array
    {
        return [
            'ponctuel' => 'Ponctuel',
            'hebdomadaire' => 'Hebdomadaire',
            'bihebdomadaire' => 'Toutes les 2 semaines',
            'mensuel' => 'Mensuel',
        ];
    }

    public function getPrioritesProperty(): array
    {
        return [
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente',
        ];
    }

    public function getOptionsDisponiblesProperty(): array
    {
        return [
            'vitres' => 'Vitres',
            'frigo' => 'Frigo',
            'four' => 'Four',
            'repassage' => 'Repassage',
            'desinfection' => 'Désinfection',
        ];
    }

    public function getZonesDisponiblesProperty(): array
    {
        return [
            'cuisine' => 'Cuisine',
            'salle_de_bain' => 'Salle de bain',
            'salon' => 'Salon',
            'chambres' => 'Chambres',
            'bureau' => 'Bureau',
            'escaliers' => 'Escaliers',
        ];
    }

    public function getEmployesProperty()
    {
        $query = User::query()
            ->where('role', 'employe')
            ->orderBy('name');

        $employes = $query->get();

        if (! $this->isPremiumClient() || ! Auth::check()) {
            return $employes;
        }

        $favoriteIds = Auth::user()->favoriteEmployes()->pluck('users.id')->toArray();

        $favorites = $employes->filter(fn($e) => in_array($e->id, $favoriteIds));
        $others = $employes->reject(fn($e) => in_array($e->id, $favoriteIds));

        return $favorites->concat($others)->values();
    }

    protected function rules(): array
    {
        return [
            'service_type' => ['required', 'string', 'max:255'],
            'type_lieu' => ['required', 'string', 'max:255'],
            'frequence' => ['required', 'string', 'max:255'],
            'surface' => ['required', Rule::in(array_keys($this->surfaces))],

            'options_prestation' => ['nullable', 'array'],
            'zones_specifiques' => ['nullable', 'array'],
            'materiel_specifique' => ['nullable', 'string', 'max:255'],
            'commentaire_client' => ['nullable', 'string', 'max:2000'],

            'presence_animaux' => ['boolean'],
            'acces_parking' => ['boolean'],
            'materiel_fournit' => ['boolean'],

            'adresse' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:20'],
            'telephone_client' => ['required', 'string', 'max:30'],
            'priorite' => ['required', Rule::in(['normale', 'haute', 'urgente'])],

            'employe_id' => $this->isPremiumClient()
                ? ['required', 'exists:users,id']
                : ['nullable'],

            'rdvDate' => ['required', 'date'],
            'rdvHeure' => ['required', 'date_format:H:i'],

            'is_recurrent' => ['boolean'],
            'recurrence_rule' => ['nullable', 'string', 'max:255'],
            'is_favorite_slot' => ['boolean'],

            'photos.*' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function nextStep(): void
    {
        $this->resetErrorBag();

        if ($this->step === 1) {
            $this->validateOnlyStep1();
        }

        if ($this->step === 2) {
            $this->validateOnlyStep2();
        }

        if ($this->step === 3) {
            $this->validateOnlyStep3();
        }

        if ($this->step === 4) {
            $this->validateOnlyStep4();
        }

        if ($this->step < 5) {
            $this->step++;
        }

        $this->refreshEstimations();

        if ($this->step === 4 || $this->step === 5) {
            $this->chargerCreneauxDisponibles();
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    protected function validateOnlyStep1(): void
    {
        $this->validate([
            'service_type' => ['required', 'string', 'max:255'],
            'type_lieu' => ['required', 'string', 'max:255'],
            'frequence' => ['required', 'string', 'max:255'],
            'surface' => ['required', Rule::in(array_keys($this->surfaces))],
        ]);
    }

    protected function validateOnlyStep2(): void
    {
        $this->validate([
            'options_prestation' => ['nullable', 'array'],
            'zones_specifiques' => ['nullable', 'array'],
            'materiel_specifique' => ['nullable', 'string', 'max:255'],
            'commentaire_client' => ['nullable', 'string', 'max:2000'],
            'presence_animaux' => ['boolean'],
            'acces_parking' => ['boolean'],
            'materiel_fournit' => ['boolean'],
            'photos.*' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    protected function validateOnlyStep3(): void
    {
        $this->validate([
            'adresse' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:20'],
            'telephone_client' => ['required', 'string', 'max:30'],
            'priorite' => ['required', Rule::in(['normale', 'haute', 'urgente'])],
        ]);
    }

    protected function validateOnlyStep4(): void
    {
        $rules = [
            'rdvDate' => ['required', 'date'],
            'rdvHeure' => ['required', 'date_format:H:i'],
            'is_recurrent' => ['boolean'],
            'recurrence_rule' => ['nullable', 'string', 'max:255'],
            'is_favorite_slot' => ['boolean'],
        ];

        if ($this->isPremiumClient()) {
            $rules['employe_id'] = ['required', 'exists:users,id'];
        }

        $this->validate($rules);
    }

    public function updatedEmployeId(): void
    {
        if ($this->isPremiumClient()) {
            $this->chargerCreneauxDisponibles();
        }
    }

    public function updatedRdvDate(): void
    {
        $this->chargerCreneauxDisponibles();
    }

    public function updatedServiceType(): void
    {
        $this->refreshEstimations();
    }

    public function updatedSurface(): void
    {
        $this->refreshEstimations();
    }

    public function updatedOptionsPrestation(): void
    {
        $this->refreshEstimations();
    }

    public function updatedFrequence(): void
    {
        $this->refreshEstimations();
    }

    public function updatedZonesSpecifiques(): void
    {
        $this->refreshEstimations();
    }

    public function updatedPresenceAnimaux(): void
    {
        $this->refreshEstimations();
    }

    public function updatedAccesParking(): void
    {
        $this->refreshEstimations();
    }

    public function updatedMaterielFournit(): void
    {
        $this->refreshEstimations();
    }

    public function updatedMaterielSpecifique(): void
    {
        $this->refreshEstimations();
    }

    protected function chargerEmployesDisponibles(): void
    {
        $this->employesDisponibles = $this->employes
            ->map(fn($employe) => [
                'id' => $employe->id,
                'name' => $employe->name,
            ])->toArray();
    }

    protected function chargerCreneauxDisponibles(): void
    {
        $this->creneauxDisponibles = [];

        if (! $this->rdvDate) {
            return;
        }

        $date = $this->rdvDate;
        $slots = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

        if ($this->isPremiumClient() && $this->employe_id) {
            $slots = collect($slots)->filter(function ($heure) use ($date) {
                return ! RendezVous::where('employe_id', $this->employe_id)
                    ->where('date', $date)
                    ->where('heure', $heure)
                    ->whereIn('status', ['en_attente', 'confirme', 'en_route', 'sur_place'])
                    ->exists();
            })->values()->toArray();
        }

        $this->creneauxDisponibles = $slots;
    }

    protected function refreshEstimations(): void
    {
        $this->duree_estimee = $this->calculerDureeEstimee();
        $this->devis_estime = $this->calculerDevisEstime();
    }

    protected function calculerDureeEstimee(): int
    {
        $baseMinutes = match ($this->service_type) {
            'nettoyage_standard' => 120,
            'nettoyage_profond' => 180,
            'fin_de_chantier' => 240,
            'fin_de_bail' => 240,
            'bureaux' => 150,
            default => 120,
        };

        $surfaceMinutes = match ($this->surface) {
            'moins_50' => 0,
            '50_100' => 30,
            '100_150' => 60,
            '150_250' => 90,
            'plus_250' => 120,
            default => 0,
        };

        $optionsMinutes = count($this->options_prestation) * 20;
        $zonesMinutes = count($this->zones_specifiques) * 10;
        $animauxMinutes = $this->presence_animaux ? 10 : 0;

        return $baseMinutes + $surfaceMinutes + $optionsMinutes + $zonesMinutes + $animauxMinutes;
    }

    protected function calculerDevisEstime(): float
    {
        $basePrice = match ($this->service_type) {
            'nettoyage_standard' => 79,
            'nettoyage_profond' => 129,
            'fin_de_chantier' => 189,
            'fin_de_bail' => 179,
            'bureaux' => 149,
            default => 79,
        };

        $surfacePrice = match ($this->surface) {
            'moins_50' => 0,
            '50_100' => 20,
            '100_150' => 40,
            '150_250' => 70,
            'plus_250' => 100,
            default => 0,
        };

        $optionsPrice = count($this->options_prestation) * 15;
        $zonesPrice = count($this->zones_specifiques) * 8;
        $premiumPrice = $this->isPremiumClient() ? 10 : 0;

        $subtotal = $basePrice + $surfacePrice + $optionsPrice + $zonesPrice + $premiumPrice;

        if ($this->frequence === 'hebdomadaire') {
            $subtotal *= 0.92;
        } elseif ($this->frequence === 'bihebdomadaire') {
            $subtotal *= 0.95;
        } elseif ($this->frequence === 'mensuel') {
            $subtotal *= 0.97;
        }

        return round($subtotal, 2);
    }

    public function validerRdv(): void
    {
        Gate::authorize('create', RendezVous::class);

        $this->validate();

        if ($this->isPremiumClient() && $this->employe_id) {
            $conflit = RendezVous::where('employe_id', $this->employe_id)
                ->where('date', $this->rdvDate)
                ->where('heure', $this->rdvHeure)
                ->whereIn('status', ['en_attente', 'confirme', 'en_route', 'sur_place'])
                ->exists();

            if ($conflit) {
                $this->addError('rdvHeure', 'Ce créneau n’est plus disponible pour cet employé.');
                return;
            }
        }

        $photoPaths = [];
        foreach ($this->photos as $photo) {
            $photoPaths[] = $photo->store('rendezvous/references', 'public');
        }

        RendezVous::create([
            'client_id' => Auth::id(),
            'employe_id' => $this->isPremiumClient() ? $this->employe_id : null,
            'date' => $this->rdvDate,
            'heure' => $this->rdvHeure,
            'service_type' => $this->service_type,
            'type_lieu' => $this->type_lieu,
            'frequence' => $this->frequence,
            'surface' => $this->surface,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'code_postal' => $this->code_postal,
            'telephone_client' => $this->telephone_client,
            'priorite' => $this->priorite,
            'commentaire_client' => $this->commentaire_client,
            'options_prestation' => $this->options_prestation,
            'zones_specifiques' => $this->zones_specifiques,
            'materiel_specifique' => $this->materiel_specifique ? [$this->materiel_specifique] : [],
            'presence_animaux' => $this->presence_animaux,
            'acces_parking' => $this->acces_parking,
            'materiel_fournit' => $this->materiel_fournit,
            'is_recurrent' => $this->is_recurrent,
            'recurrence_rule' => $this->recurrence_rule,
            'is_favorite_slot' => $this->is_favorite_slot,
            'photos_reference' => $photoPaths,
            'duree_estimee' => $this->duree_estimee,
            'devis_estime' => $this->devis_estime,
            'status' => 'en_attente',
        ]);

        session()->flash('success', 'Votre demande a bien été envoyée.');
        $this->dispatch('toast', 'Votre demande a bien été envoyée.', 'success');
        $this->step = 5;
    }



    public function render()
    {
        return view('livewire.client.prendre-rendez-vous', [
            'surfaces' => $this->surfaces,
            'services' => $this->services,
            'typesLieu' => $this->typesLieux,
            'frequences' => $this->frequences,
            'priorites' => $this->priorites,
            'optionsDisponibles' => $this->optionsDisponibles,
            'zonesDisponibles' => $this->zonesDisponibles,
            'employesDisponibles' => $this->employesDisponibles,
            'creneauxDisponibles' => $this->creneauxDisponibles,
        ])->layout('layouts.app');
    }
}
