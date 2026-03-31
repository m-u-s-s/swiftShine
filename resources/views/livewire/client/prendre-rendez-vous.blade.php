<div class="space-y-6" x-data="{ step: @entangle('step') }">
    <div class="space-y-2">
        <h2 class="text-2xl font-bold text-gray-800">🧼 Réserver un service de nettoyage</h2>
        <p class="text-sm text-gray-500">
            Réservez votre intervention en quelques étapes simples.
        </p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
        <div class="rounded-lg px-3 py-2 text-center border" :class="step === 1 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200'">1. Service</div>
        <div class="rounded-lg px-3 py-2 text-center border" :class="step === 2 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200'">2. Adresse</div>
        <div class="rounded-lg px-3 py-2 text-center border" :class="step === 3 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200'">3. Employé</div>
        <div class="rounded-lg px-3 py-2 text-center border" :class="step === 4 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200'">4. Créneau</div>
        <div class="rounded-lg px-3 py-2 text-center border" :class="step === 5 ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200'">5. Confirmation</div>
    </div>

    <div x-show="step === 1" x-transition class="bg-white rounded-xl border shadow p-5 space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">🧽 Quel service vous faut-il ?</h3>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type de service</label>
            <select wire:model="service_type" class="w-full border-gray-300 rounded-lg shadow-sm">
                <option value="">-- Sélectionnez un service --</option>
                @foreach($services as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('service_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de lieu</label>
                <select wire:model="type_lieu" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">-- Sélectionnez --</option>
                    @foreach($typesLieux as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('type_lieu') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fréquence</label>
                <select wire:model="frequence" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">-- Sélectionnez --</option>
                    @foreach($frequences as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('frequence') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Surface / nombre de pièces</label>
            <input
                type="text"
                wire:model="surface"
                placeholder="Ex. 120 m², 4 pièces, 2 étages..."
                class="w-full border-gray-300 rounded-lg shadow-sm">
            @error('surface') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Options de prestation</p>
                <div class="space-y-2">
                    @foreach($optionsPrestationsDisponibles as $value => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model="options_prestation" value="{{ $value }}" class="rounded border-gray-300">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Zones spécifiques</p>
                <div class="space-y-2">
                    @foreach($zonesDisponibles as $value => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model="zones_specifiques" value="{{ $value }}" class="rounded border-gray-300">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Matériel / produits</p>
                <div class="space-y-2">
                    @foreach($materielsDisponibles as $value => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" wire:model="materiel_specifique" value="{{ $value }}" class="rounded border-gray-300">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        @if($duree_estimee)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
                ⏱️ Durée estimée :
                <strong>{{ $duree_estimee }} minutes</strong>
            </div>
        @endif

        @if($devis_estime)
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 text-sm text-emerald-800">
                💶 Devis estimatif :
                <strong>{{ number_format($devis_estime, 2, ',', ' ') }} €</strong>
            </div>
        @endif

        <div class="text-right">
            <button wire:click="nextStep" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Suivant →
            </button>
        </div>
    </div>

    <div x-show="step === 2" x-transition class="bg-white rounded-xl border shadow p-5 space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">📍 Où doit avoir lieu l’intervention ?</h3>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
            <input type="text" wire:model="adresse" placeholder="Rue, numéro, boîte..." class="w-full border-gray-300 rounded-lg shadow-sm">
            @error('adresse') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                <input type="text" wire:model="ville" class="w-full border-gray-300 rounded-lg shadow-sm">
                @error('ville') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                <input type="text" wire:model="code_postal" class="w-full border-gray-300 rounded-lg shadow-sm">
                @error('code_postal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone de contact</label>
                <input type="text" wire:model="telephone_client" class="w-full border-gray-300 rounded-lg shadow-sm">
                @error('telephone_client') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                <select wire:model="priorite" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">-- Sélectionnez --</option>
                    @foreach($priorites as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('priorite') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="presence_animaux" class="rounded border-gray-300">
                Présence d’animaux
            </label>

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="acces_parking" class="rounded border-gray-300">
                Parking disponible
            </label>

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="materiel_fournit" class="rounded border-gray-300">
                Matériel fourni par le client
            </label>

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="is_recurrent" class="rounded border-gray-300">
                Prestation récurrente
            </label>

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="is_favorite_slot" class="rounded border-gray-300">
                Sauvegarder comme créneau favori
            </label>
        </div>

        @if($is_recurrent)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Règle de récurrence</label>
                <select wire:model="recurrence_rule" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">-- Sélectionnez --</option>
                    <option value="weekly">Chaque semaine</option>
                    <option value="biweekly">Toutes les 2 semaines</option>
                    <option value="monthly">Chaque mois</option>
                </select>
            </div>
        @endif

        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Photos de référence (optionnel)
                </label>
                <input type="file" wire:model="photos" multiple accept="image/*" class="w-full text-sm border-gray-300 rounded-lg shadow-sm">
                @error('photos.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            @if($photos)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($photos as $index => $photo)
                        <div class="relative border rounded-lg p-2 bg-gray-50">
                            <img src="{{ $photo->temporaryUrl() }}" alt="Aperçu photo" class="w-full h-28 object-cover rounded">
                            <button type="button" wire:click="removePhoto({{ $index }})" class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded">
                                ✕
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire / détails utiles</label>
            <textarea wire:model="commentaire_client" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm"></textarea>
            @error('commentaire_client') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4 flex justify-between">
            <button wire:click="prevStep" class="text-sm text-gray-500 hover:underline">← Retour</button>
            <button wire:click="nextStep" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Suivant →</button>
        </div>
    </div>

    <div x-show="step === 3" x-transition class="bg-white rounded-xl border shadow p-5 space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">👤 Choisissez un employé</h3>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
            <select wire:model="employe_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                <option value="">-- Sélectionnez un employé --</option>
                @foreach($employes as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                @endforeach
            </select>
            @error('employe_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4 flex justify-between">
            <button wire:click="prevStep" class="text-sm text-gray-500 hover:underline">← Retour</button>
            <button wire:click="nextStep" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Suivant →</button>
        </div>
    </div>

    <div x-show="step === 4" x-transition class="bg-white rounded-xl border shadow p-5 space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">📆 Sélectionnez votre créneau</h3>

        @if($employe_id)
            @livewire('client.calendrier-prise-rdv', [
                'employe_id' => $employe_id,
                'selectedDate' => $rdvDate,
                'selectedHeure' => $rdvHeure,
                'dureeEstimee' => $duree_estimee
            ], key('calendar-'.$employe_id.'-'.$duree_estimee))
        @endif

        @error('rdvDate') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        @error('rdvHeure') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

        <div class="mt-4 flex justify-between">
            <button wire:click="prevStep" class="text-sm text-gray-500 hover:underline">← Retour</button>
            <button wire:click="nextStep" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Suivant →</button>
        </div>
    </div>

    <div x-show="step === 5" x-transition class="bg-white rounded-xl border shadow p-5 space-y-5">
        <h3 class="text-lg font-semibold text-gray-800">✅ Confirmez votre demande</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                <h4 class="font-semibold text-gray-800">🧽 Service</h4>
                <p><span class="font-medium">Type :</span> {{ $services[$service_type] ?? '—' }}</p>
                <p><span class="font-medium">Lieu :</span> {{ $typesLieux[$type_lieu] ?? '—' }}</p>
                <p><span class="font-medium">Fréquence :</span> {{ $frequences[$frequence] ?? '—' }}</p>
                <p><span class="font-medium">Surface :</span> {{ $surface ?: '—' }}</p>
                <p><span class="font-medium">Durée estimée :</span> {{ $duree_estimee ? $duree_estimee . ' min' : '—' }}</p>
                <p><span class="font-medium">Devis estimatif :</span> {{ $devis_estime ? number_format($devis_estime, 2, ',', ' ') . ' €' : '—' }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                <h4 class="font-semibold text-gray-800">📍 Adresse</h4>
                <p><span class="font-medium">Adresse :</span> {{ $adresse ?: '—' }}</p>
                <p><span class="font-medium">Ville :</span> {{ $ville ?: '—' }}</p>
                <p><span class="font-medium">Code postal :</span> {{ $code_postal ?: '—' }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                <h4 class="font-semibold text-gray-800">👤 Intervention</h4>
                <p><span class="font-medium">Employé :</span> {{ $employeNom ?: '—' }}</p>
                <p><span class="font-medium">Date :</span> {{ $rdvDate ?: '—' }}</p>
                <p><span class="font-medium">Heure :</span> {{ $rdvHeure ?: '—' }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                <h4 class="font-semibold text-gray-800">📝 Détails métier</h4>
                <p><span class="font-medium">Options :</span> {{ !empty($options_prestation) ? implode(', ', $options_prestation) : '—' }}</p>
                <p><span class="font-medium">Zones :</span> {{ !empty($zones_specifiques) ? implode(', ', $zones_specifiques) : '—' }}</p>
                <p><span class="font-medium">Matériel :</span> {{ !empty($materiel_specifique) ? implode(', ', $materiel_specifique) : '—' }}</p>
                <p><span class="font-medium">Récurrent :</span> {{ $is_recurrent ? 'Oui' : 'Non' }}</p>
            </div>
        </div>

        <div class="mt-4 flex justify-between">
            <button wire:click="prevStep" class="text-sm text-gray-500 hover:underline">← Modifier</button>
            <button wire:click="validerRdv" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700">Confirmer la demande</button>
        </div>
    </div>

    @if($rdvEnvoye)
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition.duration.500ms
            x-init="setTimeout(() => show = false, 5000); $el.scrollIntoView({ behavior: 'smooth' })"
            class="mt-6 bg-green-100 border border-green-300 text-green-800 text-center p-4 rounded-xl shadow">
            🎉 Votre demande de nettoyage a été envoyée avec succès !
        </div>
    @endif
</div>