<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <div class="xl:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 md:px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-medium text-sky-600">
                                Réservation {{ $this->isPremiumClient() ? 'Premium' : 'Standard' }}
                            </p>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                                Planifier une prestation
                            </h1>
                            <p class="text-sm text-slate-500 mt-1">
                                Remplissez votre demande en quelques étapes.
                            </p>
                        </div>

                        @if($this->isPremiumClient())
                            <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 text-amber-700 px-4 py-2 text-sm font-semibold border border-amber-200">
                                <span>★</span>
                                <span>Client Premium actif</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            @php
                                $steps = [
                                    1 => 'Service',
                                    2 => 'Détails',
                                    3 => 'Coordonnées',
                                    4 => $this->isPremiumClient() ? 'Employé & créneau' : 'Créneau',
                                    5 => 'Confirmation',
                                ];
                            @endphp

                            @foreach($steps as $number => $label)
                                <div class="rounded-2xl border px-4 py-3 text-sm transition
                                    {{ $step === $number
                                        ? 'border-sky-500 bg-sky-50 text-sky-700'
                                        : ($step > $number
                                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                            : 'border-slate-200 bg-white text-slate-500')
                                    }}">
                                    <div class="font-semibold">Étape {{ $number }}</div>
                                    <div class="text-xs mt-1">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="px-6 md:px-8 py-8">
                    @if($step === 1)
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Type de service</label>
                                    <select wire:model.live="service_type" class="w-full rounded-2xl border-slate-300">
                                        <option value="">Choisir un service</option>
                                        @foreach($services as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('service_type') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Type de lieu</label>
                                    <select wire:model.live="type_lieu" class="w-full rounded-2xl border-slate-300">
                                        <option value="">Choisir un type de lieu</option>
                                        @foreach($typesLieu as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type_lieu') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Fréquence</label>
                                    <select wire:model.live="frequence" class="w-full rounded-2xl border-slate-300">
                                        <option value="">Choisir une fréquence</option>
                                        @foreach($frequences as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('frequence') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Surface</label>
                                    <select wire:model.live="surface" class="w-full rounded-2xl border-slate-300">
                                        <option value="">Choisir une surface</option>
                                        @foreach($surfaces as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('surface') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($step === 2)
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-3">Options</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($optionsDisponibles as $key => $label)
                                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3">
                                            <input type="checkbox" wire:model.live="options_prestation" value="{{ $key }}">
                                            <span class="text-sm text-slate-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-3">Zones à traiter</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($zonesDisponibles as $key => $label)
                                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3">
                                            <input type="checkbox" wire:model.live="zones_specifiques" value="{{ $key }}">
                                            <span class="text-sm text-slate-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Matériel spécifique</label>
                                    <input type="text" wire:model.defer="materiel_specifique" class="w-full rounded-2xl border-slate-300">
                                </div>

                                <div class="rounded-2xl border border-slate-200 p-4 space-y-3">
                                    <label class="flex items-center gap-3">
                                        <input type="checkbox" wire:model.live="presence_animaux">
                                        <span class="text-sm text-slate-700">Présence d’animaux</span>
                                    </label>

                                    <label class="flex items-center gap-3">
                                        <input type="checkbox" wire:model.live="acces_parking">
                                        <span class="text-sm text-slate-700">Parking / accès facile</span>
                                    </label>

                                    <label class="flex items-center gap-3">
                                        <input type="checkbox" wire:model.live="materiel_fournit">
                                        <span class="text-sm text-slate-700">Matériel fourni sur place</span>
                                    </label>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Photos de référence</label>
                                    <input type="file" wire:model="photos" multiple class="w-full rounded-2xl border-slate-300">
                                    @error('photos.*') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Commentaire</label>
                                <textarea wire:model.defer="commentaire_client" rows="5" class="w-full rounded-2xl border-slate-300"></textarea>
                            </div>
                        </div>
                    @endif

                    @if($step === 3)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Adresse</label>
                                <input type="text" wire:model.defer="adresse" class="w-full rounded-2xl border-slate-300">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Ville</label>
                                <input type="text" wire:model.defer="ville" class="w-full rounded-2xl border-slate-300">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Code postal</label>
                                <input type="text" wire:model.defer="code_postal" class="w-full rounded-2xl border-slate-300">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Téléphone</label>
                                <input type="text" wire:model.defer="telephone_client" class="w-full rounded-2xl border-slate-300">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Priorité</label>
                                <select wire:model.defer="priorite" class="w-full rounded-2xl border-slate-300">
                                    @foreach($priorites as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    @if($step === 4)
                        <div class="space-y-6">
                            @if($this->isPremiumClient())
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Employé</label>
                                    <select wire:model.live="employe_id" class="w-full rounded-2xl border-slate-300">
                                        <option value="">Choisir un employé</option>
                                        @foreach($employesDisponibles as $employe)
                                            <option value="{{ $employe['id'] }}">{{ $employe['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('employe_id') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Date souhaitée</label>
                                    <input type="date" wire:model.live="rdvDate" min="{{ now()->toDateString() }}" class="w-full rounded-2xl border-slate-300">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Heure souhaitée</label>
                                    <select wire:model.defer="rdvHeure" class="w-full rounded-2xl border-slate-300">
                                        <option value="">Choisir un créneau</option>
                                        @foreach($creneauxDisponibles as $creneau)
                                            <option value="{{ $creneau }}">{{ $creneau }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-4 space-y-3">
                                <label class="flex items-center gap-3">
                                    <input type="checkbox" wire:model.live="is_recurrent">
                                    <span class="text-sm text-slate-700">Intervention récurrente</span>
                                </label>

                                @if($is_recurrent)
                                    <select wire:model.defer="recurrence_rule" class="w-full rounded-2xl border-slate-300">
                                        <option value="">Choisir une règle</option>
                                        <option value="weekly">Chaque semaine</option>
                                        <option value="biweekly">Toutes les 2 semaines</option>
                                        <option value="monthly">Chaque mois</option>
                                    </select>
                                @endif

                                <label class="flex items-center gap-3">
                                    <input type="checkbox" wire:model.live="is_favorite_slot">
                                    <span class="text-sm text-slate-700">Enregistrer ce créneau comme favori</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    @if($step === 5)
                        <div class="space-y-6">
                            @if(session()->has('success'))
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-700 px-5 py-4">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div><span class="text-slate-500">Service</span><p class="font-semibold">{{ $services[$service_type] ?? '-' }}</p></div>
                                    <div><span class="text-slate-500">Lieu</span><p class="font-semibold">{{ $typesLieu[$type_lieu] ?? '-' }}</p></div>
                                    <div><span class="text-slate-500">Fréquence</span><p class="font-semibold">{{ $frequences[$frequence] ?? '-' }}</p></div>
                                    <div><span class="text-slate-500">Surface</span><p class="font-semibold">{{ $surfaces[$surface] ?? '-' }}</p></div>
                                    <div><span class="text-slate-500">Adresse</span><p class="font-semibold">{{ $adresse ?: '-' }}</p></div>
                                    <div><span class="text-slate-500">Ville</span><p class="font-semibold">{{ $ville ?: '-' }}</p></div>
                                    <div><span class="text-slate-500">Date</span><p class="font-semibold">{{ $rdvDate ?: '-' }}</p></div>
                                    <div><span class="text-slate-500">Heure</span><p class="font-semibold">{{ $rdvHeure ?: '-' }}</p></div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-10 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            @if($step > 1 && $step < 5)
                                <button type="button" wire:click="previousStep" class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">
                                    Retour
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            @if($step < 4)
                                <button type="button" wire:click="nextStep" class="rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white">
                                    Continuer
                                </button>
                            @endif

                            @if($step === 4)
                                <button type="button" wire:click="nextStep" class="rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white">
                                    Voir le résumé
                                </button>
                            @endif

                            @if($step === 5)
                                <button type="button" wire:click="validerRdv" class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">
                                    Confirmer ma demande
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-1">
            <div class="sticky top-6 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                    <p class="text-sm font-medium text-slate-500">Résumé de votre demande</p>
                    <h3 class="text-xl font-bold text-slate-900 mt-1">Estimation en direct</h3>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                            <p class="text-sm text-slate-500">Durée estimée</p>
                            <p class="text-2xl font-bold text-slate-900 mt-1">
                                {{ $duree_estimee > 0 ? $duree_estimee . ' min' : '--' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-sky-50 p-4 border border-sky-100">
                            <p class="text-sm text-sky-700">Devis estimatif</p>
                            <p class="text-3xl font-extrabold text-sky-900 mt-1">
                                {{ number_format((float) $devis_estime, 2, ',', ' ') }} €
                            </p>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-slate-500">Service</span>
                                <span class="font-semibold text-slate-800 text-right">{{ $services[$service_type] ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-slate-500">Lieu</span>
                                <span class="font-semibold text-slate-800 text-right">{{ $typesLieu[$type_lieu] ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-slate-500">Fréquence</span>
                                <span class="font-semibold text-slate-800 text-right">{{ $frequences[$frequence] ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-slate-500">Surface</span>
                                <span class="font-semibold text-slate-800 text-right">{{ $surfaces[$surface] ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-slate-500">Options</span>
                                <span class="font-semibold text-slate-800 text-right">{{ count($options_prestation) }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-slate-500">Zones</span>
                                <span class="font-semibold text-slate-800 text-right">{{ count($zones_specifiques) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!$this->isPremiumClient())
                    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6">
                        <p class="text-sm font-semibold text-amber-800">Passez en Premium</p>
                        <p class="text-sm text-amber-700 mt-2">
                            Choisissez vos employés favoris et consultez leurs disponibilités.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>