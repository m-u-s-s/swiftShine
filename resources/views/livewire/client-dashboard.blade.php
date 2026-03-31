<div class="p-4 md:p-6 space-y-6">

    <x-active-sessions />

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-blue-900">🎯 Mon espace client</h2>
            <p class="text-sm text-gray-500">
                Suivez vos missions, consultez les rapports et reprogrammez rapidement vos prochaines demandes.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700"
            >
                ➕ Nouveau rendez-vous
            </a>
        </div>
    </div>

    <x-toast />

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl shadow border">
            <p class="text-sm text-gray-500">Total missions</p>
            <p class="text-2xl font-bold text-slate-800">{{ $statsClient['total'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border">
            <p class="text-sm text-gray-500">À venir</p>
            <p class="text-2xl font-bold text-blue-700">{{ $statsClient['avenir'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border">
            <p class="text-sm text-gray-500">Terminées</p>
            <p class="text-2xl font-bold text-emerald-700">{{ $statsClient['termine'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border">
            <p class="text-sm text-gray-500">Feedbacks laissés</p>
            <p class="text-2xl font-bold text-amber-600">{{ $statsClient['feedbacks'] }}</p>
        </div>
    </div>

    @if($dernierRendezVous)
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-gradient-to-r from-slate-900 to-slate-700 text-white rounded-2xl shadow p-5">
                <p class="text-sm text-slate-300">Réservation rapide</p>
                <h3 class="text-xl font-bold mt-1">Même service que la dernière fois</h3>

                <div class="mt-4 space-y-2 text-sm text-slate-200">
                    <p><span class="font-medium text-white">Service :</span> {{ ucfirst(str_replace('_', ' ', $dernierRendezVous->service_type ?? '—')) }}</p>
                    <p><span class="font-medium text-white">Adresse :</span> {{ $dernierRendezVous->adresse ?? '—' }}, {{ $dernierRendezVous->ville ?? '—' }}</p>
                    <p><span class="font-medium text-white">Type :</span> {{ ucfirst($dernierRendezVous->type_lieu ?? '—') }}</p>
                    <p><span class="font-medium text-white">Fréquence :</span> {{ ucfirst(str_replace('_', ' ', $dernierRendezVous->frequence ?? '—')) }}</p>
                </div>

                <div class="mt-4">
                    <a
                        href="{{ route('home') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-white text-slate-800 text-sm font-medium hover:bg-slate-100"
                    >
                        🔁 Reprendre une réservation similaire
                    </a>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow border">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">📍 Adresses récentes</h3>

                <div class="space-y-3">
                    @forelse($adressesRecentes as $adresse)
                        <div class="border rounded-xl p-3 bg-gray-50">
                            <p class="font-medium text-gray-800">{{ $adresse->adresse }}</p>
                            <p class="text-sm text-gray-600">{{ $adresse->ville ?? '—' }} {{ $adresse->code_postal ?? '' }}</p>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 italic">
                            Aucune adresse récente.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white p-5 rounded-2xl shadow border">
        <h3 class="text-lg font-semibold mb-4">📅 Mes missions à venir</h3>

        <div class="space-y-4">
            @forelse($avenir as $rdv)
                <div class="border rounded-2xl p-4 shadow-sm bg-gray-50 space-y-4">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                        <div>
                            <h4 class="font-semibold text-gray-800 text-lg">
                                {{ ucfirst(str_replace('_', ' ', $rdv->service_type ?? 'Service non précisé')) }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                📅 {{ $rdv->date }} à {{ $rdv->heure }}
                            </p>
                            <p class="text-sm text-gray-600">
                                🧑‍💼 {{ $rdv->employe->name ?? 'Employé à confirmer' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <x-badge :status="$rdv->status" />
                            <x-priority-badge :priority="$rdv->priorite" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                        <div class="space-y-1">
                            <p><span class="font-medium">Type de lieu :</span> {{ ucfirst($rdv->type_lieu ?? '—') }}</p>
                            <p><span class="font-medium">Fréquence :</span> {{ ucfirst(str_replace('_', ' ', $rdv->frequence ?? '—')) }}</p>
                            <p><span class="font-medium">Surface :</span> {{ $rdv->surface ?? '—' }}</p>
                            <p><span class="font-medium">Durée estimée :</span> {{ $rdv->duree_estimee ? $rdv->duree_estimee . ' min' : '—' }}</p>
                        </div>

                        <div class="space-y-1">
                            <p><span class="font-medium">Adresse :</span> {{ $rdv->adresse ?? '—' }}</p>
                            <p><span class="font-medium">Ville :</span> {{ $rdv->ville ?? '—' }}</p>
                            <p><span class="font-medium">Code postal :</span> {{ $rdv->code_postal ?? '—' }}</p>
                            <p><span class="font-medium">Téléphone :</span> {{ $rdv->telephone_client ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="bg-white border rounded-xl p-4">
                        <p class="text-sm font-semibold text-slate-800 mb-3">🧭 Suivi de mission</p>

                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="px-3 py-1 rounded-full {{ in_array($rdv->status, ['en_attente','confirme','en_route','sur_place','termine']) ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }}">
                                Demande reçue
                            </span>
                            <span class="px-3 py-1 rounded-full {{ in_array($rdv->status, ['confirme','en_route','sur_place','termine']) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                Confirmée
                            </span>
                            <span class="px-3 py-1 rounded-full {{ in_array($rdv->status, ['en_route','sur_place','termine']) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                En route
                            </span>
                            <span class="px-3 py-1 rounded-full {{ in_array($rdv->status, ['sur_place','termine']) ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                                Sur place
                            </span>
                            <span class="px-3 py-1 rounded-full {{ $rdv->status === 'termine' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                Terminée
                            </span>
                        </div>
                    </div>

                    @if($rdv->commentaire_client)
                        <div class="text-sm text-gray-700 bg-white border rounded-xl p-3">
                            <span class="font-medium">Remarque :</span> {{ $rdv->commentaire_client }}
                        </div>
                    @endif

                    @if(!empty($rdv->photos_reference))
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-700">📷 Photos de référence</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($rdv->photos_reference as $photo)
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="block">
                                        <img
                                            src="{{ asset('storage/' . $photo) }}"
                                            alt="Photo de référence"
                                            class="w-full h-28 object-cover rounded-lg border hover:opacity-90 transition">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-3 text-sm">
                        <button wire:click="modifier({{ $rdv->id }})" class="text-blue-600 underline">
                            ✏️ Modifier
                        </button>

                        <button
                            onclick="if(confirm('Confirmer l\\'annulation ?')) { $wire.annuler({{ $rdv->id }}) }"
                            class="text-red-600 underline">
                            ❌ Annuler
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center italic text-gray-500 py-4">
                    Aucun rendez-vous à venir.
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $avenir->links() }}</div>
    </div>

    @if($editRdvId)
        <div class="bg-yellow-50 p-4 border border-yellow-300 rounded-xl shadow">
            <h4 class="font-semibold text-yellow-800 mb-3">✏️ Modifier le rendez-vous</h4>
            <div class="flex gap-4 items-end flex-wrap">
                <div>
                    <label class="text-sm text-gray-700">Date</label>
                    <input type="date" wire:model="editDate" class="text-sm border-gray-300 rounded px-2 py-1">
                </div>
                <div>
                    <label class="text-sm text-gray-700">Heure</label>
                    <input type="time" wire:model="editHeure" class="text-sm border-gray-300 rounded px-2 py-1">
                </div>
                <button wire:click="enregistrerModif" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                    💾 Sauvegarder
                </button>
                <button wire:click="fermerEdition" class="text-sm text-gray-600 underline">
                    Annuler
                </button>
            </div>
        </div>
    @endif

    <div class="bg-white p-5 rounded-2xl shadow border">
        <h3 class="text-lg font-semibold mb-4">📜 Missions passées et rapports</h3>

        <div class="space-y-4">
            @forelse($passe as $rdv)
                <div class="border rounded-2xl p-4 bg-gray-50 text-sm text-gray-700 space-y-4">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                        <div>
                            <p class="font-medium text-gray-800 text-lg">
                                {{ ucfirst(str_replace('_', ' ', $rdv->service_type ?? 'Service non précisé')) }}
                            </p>
                            <p>{{ $rdv->date }} à {{ $rdv->heure }}</p>
                            <p>🧑‍💼 {{ $rdv->employe->name ?? '—' }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <x-badge :status="$rdv->status" />
                            <x-priority-badge :priority="$rdv->priorite" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <p><span class="font-medium">Adresse :</span> {{ $rdv->adresse ?? '—' }}, {{ $rdv->ville ?? '—' }}</p>
                            <p><span class="font-medium">Type de lieu :</span> {{ ucfirst($rdv->type_lieu ?? '—') }}</p>
                            <p><span class="font-medium">Durée estimée :</span> {{ $rdv->duree_estimee ? $rdv->duree_estimee . ' min' : '—' }}</p>
                            <p><span class="font-medium">Durée réelle :</span> {{ $rdv->duree_reelle ? $rdv->duree_reelle . ' min' : '—' }}</p>
                        </div>

                        <div>
                            <p><span class="font-medium">Fréquence :</span> {{ ucfirst(str_replace('_', ' ', $rdv->frequence ?? '—')) }}</p>
                            <p><span class="font-medium">Surface :</span> {{ $rdv->surface ?? '—' }}</p>
                        </div>
                    </div>

                    @if($rdv->commentaire_fin_mission)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3">
                            <span class="font-medium text-emerald-800">Rapport de fin d’intervention :</span>
                            <p class="mt-1 text-emerald-900">{{ $rdv->commentaire_fin_mission }}</p>
                        </div>
                    @endif

                    @if($rdv->feedback)
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                            <span class="font-medium text-amber-800">Votre feedback :</span>
                            <p class="mt-1">Note : {{ $rdv->feedback->note ?? '—' }}/5</p>
                            <p>{{ $rdv->feedback->commentaire ?? 'Aucun commentaire.' }}</p>

                            @if($rdv->feedback->reponse_admin)
                                <div class="mt-2 pt-2 border-t border-amber-200">
                                    <span class="font-medium text-amber-800">Réponse admin :</span>
                                    <p>{{ $rdv->feedback->reponse_admin }}</p>
                                </div>
                            @endif
                        </div>
                    @elseif($rdv->status === 'termine')
                        <div class="text-sm">
                            <a
                                href="{{ route('feedback.create', $rdv->id) }}"
                                class="text-blue-600 underline"
                            >
                                💬 Laisser un feedback
                            </a>
                        </div>
                    @endif

                    @if(!empty($rdv->photos_reference))
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-700">📷 Photos de référence</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($rdv->photos_reference as $photo)
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="block">
                                        <img
                                            src="{{ asset('storage/' . $photo) }}"
                                            alt="Photo de référence"
                                            class="w-full h-28 object-cover rounded-lg border hover:opacity-90 transition">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($rdv->photos_apres))
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-700">📸 Photos après intervention</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($rdv->photos_apres as $photo)
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="block">
                                        <img
                                            src="{{ asset('storage/' . $photo) }}"
                                            alt="Photo après intervention"
                                            class="w-full h-28 object-cover rounded-lg border hover:opacity-90 transition">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center italic text-gray-500 py-4">
                    Aucune intervention passée.
                </div>
            @endforelse
        </div>
    </div>
</div>