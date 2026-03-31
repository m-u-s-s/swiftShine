<div class="space-y-4">
    <div class="flex flex-wrap items-center gap-4">
        <div>
            <label class="text-sm text-gray-700 font-medium">Priorité :</label>
            <select wire:model.live="priorite" class="border rounded px-2 py-1 text-sm">
                <option value="">— Toutes —</option>
                <option value="normale">Normale</option>
                <option value="haute">Haute</option>
                <option value="urgente">Urgente</option>
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-700 font-medium">Statut :</label>
            <select wire:model.live="filtreStatus" class="border rounded px-2 py-1 text-sm">
                <option value="">— Tous —</option>
                <option value="en_attente">En attente</option>
                <option value="confirme">Confirmé</option>
                <option value="en_route">En route</option>
                <option value="sur_place">Sur place</option>
                <option value="termine">Terminé</option>
                <option value="refuse">Refusé</option>
            </select>
        </div>
    </div>

    @forelse($rendezVous as $rdv)
    <x-rdv-cleaning-card :rdv="$rdv">
        <div class="flex flex-wrap gap-3 text-sm pt-1">
            @if($rdv->status === 'en_attente')
            <button
                wire:click="mettreAJourStatut({{ $rdv->id }}, 'confirme')"
                class="px-3 py-1.5 rounded bg-green-100 text-green-700 hover:bg-green-200 transition">
                ✅ Confirmer
            </button>

            <button
                wire:click="mettreAJourStatut({{ $rdv->id }}, 'refuse')"
                class="px-3 py-1.5 rounded bg-red-100 text-red-700 hover:bg-red-200 transition">
                ❌ Refuser
            </button>
            @endif

            @if($rdv->status === 'confirme')
            <button
                wire:click="mettreAJourStatut({{ $rdv->id }}, 'en_route')"
                class="px-3 py-1.5 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                🚗 En route
            </button>
            @if($rdv->telephone_client)
            <a
                href="tel:{{ $rdv->telephone_client }}"
                class="px-3 py-1.5 rounded bg-green-100 text-green-700 hover:bg-green-200 transition">
                📞 Appeler
            </a>
            @endif

            @if($rdv->adresse || $rdv->ville)
            <a
                href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($rdv->adresse ?? '') . ' ' . ($rdv->ville ?? '')) }}"
                target="_blank"
                class="px-3 py-1.5 rounded bg-sky-100 text-sky-700 hover:bg-sky-200 transition">
                📍 GPS
            </a>
            @endif
            @endif

            @if($rdv->status === 'en_route')
            <button
                wire:click="mettreAJourStatut({{ $rdv->id }}, 'sur_place')"
                class="px-3 py-1.5 rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                📍 Sur place
            </button>
            @endif

            @if($rdv->status === 'sur_place')
            <button
                wire:click="ouvrirRapportFinMission({{ $rdv->id }})"
                class="px-3 py-1.5 rounded bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition">
                ✅ Clôturer la mission
            </button>
            @endif
        </div>
    </x-rdv-cleaning-card>
    @empty
    <div class="bg-white border rounded-xl p-6 text-center text-gray-500 italic">
        Aucun rendez-vous trouvé.
    </div>
    @endforelse

    <div class="mt-4">
        {{ $rendezVous->links() }}
    </div>

    @if($showRapportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-xl p-6 space-y-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Rapport de fin de mission</h3>
                <p class="text-sm text-gray-500">Ajoutez un résumé de l’intervention avant de la clôturer.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durée réelle (minutes)</label>
                <input
                    type="number"
                    min="15"
                    wire:model="duree_reelle"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('duree_reelle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire de fin de mission</label>
                <textarea
                    wire:model="commentaire_fin_mission"
                    rows="4"
                    class="w-full border rounded px-3 py-2 text-sm"
                    placeholder="Résumé du travail effectué, état final, remarques utiles..."></textarea>
                @error('commentaire_fin_mission') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Photos après intervention</label>
                <input
                    type="file"
                    wire:model="photos_apres"
                    multiple
                    accept="image/*"
                    class="w-full text-sm border rounded px-3 py-2">
                @error('photos_apres.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            @if($photos_apres)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($photos_apres as $photo)
                <img
                    src="{{ $photo->temporaryUrl() }}"
                    alt="Aperçu photo après intervention"
                    class="w-full h-28 object-cover rounded border">
                @endforeach
            </div>
            @endif

            <div class="flex justify-end gap-3 pt-2">
                <button
                    wire:click="fermerRapportFinMission"
                    class="px-4 py-2 rounded border text-sm text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>

                <button
                    wire:click="sauverRapportFinMission"
                    class="px-4 py-2 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                    Enregistrer et terminer
                </button>
            </div>
        </div>
    </div>
    @endif
</div>