<div class="p-4 md:p-6 space-y-6">

    <x-active-sessions />

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-blue-900">👤 Ma journée</h2>
            <p class="text-sm text-gray-500">
                Vue rapide de vos missions, actions prioritaires et historique récent.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-medium">
                {{ $statsJour['total'] }} mission(s) aujourd’hui
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-sm font-medium">
                {{ $statsJour['terminees'] }} terminée(s)
            </span>
        </div>
    </div>

    <x-toast />

    <div class="grid grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow border p-4">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-slate-800">{{ $statsJour['total'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow border p-4">
            <p class="text-sm text-gray-500">À faire</p>
            <p class="text-2xl font-bold text-amber-600">{{ $statsJour['a_faire'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow border p-4">
            <p class="text-sm text-gray-500">En cours</p>
            <p class="text-2xl font-bold text-blue-700">{{ $statsJour['en_cours'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow border p-4">
            <p class="text-sm text-gray-500">Terminées</p>
            <p class="text-2xl font-bold text-emerald-700">{{ $statsJour['terminees'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow border p-4">
            <p class="text-sm text-gray-500">Refusées</p>
            <p class="text-2xl font-bold text-red-600">{{ $statsJour['refusees'] }}</p>
        </div>
    </div>

    @if($prochaineMission)
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl shadow p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-blue-100">Prochaine mission</p>
                    <h3 class="text-xl font-bold mt-1">
                        {{ ucfirst(str_replace('_', ' ', $prochaineMission->service_type ?? 'Service')) }}
                    </h3>
                    <p class="text-sm text-blue-100 mt-1">
                        {{ $prochaineMission->date }} à {{ $prochaineMission->heure }}
                    </p>
                    <p class="text-sm text-blue-100">
                        {{ $prochaineMission->client->name ?? 'Client' }} • {{ $prochaineMission->adresse ?? 'Adresse non précisée' }}, {{ $prochaineMission->ville ?? '—' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if($prochaineMission->telephone_client)
                        <a
                            href="tel:{{ $prochaineMission->telephone_client }}"
                            class="px-4 py-2 rounded-lg bg-white text-blue-700 text-sm font-medium hover:bg-blue-50"
                        >
                            📞 Appeler
                        </a>
                    @endif

                    @if($prochaineMission->adresse || $prochaineMission->ville)
                        <a
                            href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($prochaineMission->adresse ?? '') . ' ' . ($prochaineMission->ville ?? '')) }}"
                            target="_blank"
                            class="px-4 py-2 rounded-lg bg-white text-blue-700 text-sm font-medium hover:bg-blue-50"
                        >
                            📍 GPS
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow border p-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">📅 Missions du jour</h3>
                        <p class="text-sm text-gray-500">Triées par priorité d’exécution.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($missionsDuJour as $rdv)
                        <div class="border rounded-2xl p-4 bg-gray-50 space-y-4 {{ $rdv->status === 'sur_place' ? 'ring-2 ring-indigo-200 border-indigo-300' : '' }}">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                <div>
                                    <h4 class="font-semibold text-gray-800 text-lg">
                                        {{ ucfirst(str_replace('_', ' ', $rdv->service_type ?? 'Service non précisé')) }}
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        👤 {{ $rdv->client->name ?? 'Client' }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        🕒 {{ $rdv->heure }} • 📍 {{ $rdv->adresse ?? '—' }}, {{ $rdv->ville ?? '—' }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <x-badge :status="$rdv->status" />
                                    <x-priority-badge :priority="$rdv->priorite" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
                                <div class="space-y-1">
                                    <p><span class="font-medium">Téléphone :</span> {{ $rdv->telephone_client ?? '—' }}</p>
                                    <p><span class="font-medium">Durée estimée :</span> {{ $rdv->duree_estimee ? $rdv->duree_estimee . ' min' : '—' }}</p>
                                    <p><span class="font-medium">Type de lieu :</span> {{ ucfirst($rdv->type_lieu ?? '—') }}</p>
                                </div>

                                <div class="space-y-1">
                                    <p><span class="font-medium">Surface :</span> {{ $rdv->surface ?? '—' }}</p>
                                    <p><span class="font-medium">Parking :</span> {{ $rdv->acces_parking ? 'Oui' : 'Non' }}</p>
                                    <p><span class="font-medium">Animaux :</span> {{ $rdv->presence_animaux ? 'Oui' : 'Non' }}</p>
                                </div>
                            </div>

                            @if($rdv->commentaire_client)
                                <div class="bg-white border rounded-xl p-3 text-sm text-gray-700">
                                    <span class="font-medium">Remarque client :</span>
                                    {{ $rdv->commentaire_client }}
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-2">
                                @if($rdv->telephone_client)
                                    <a
                                        href="tel:{{ $rdv->telephone_client }}"
                                        class="px-3 py-2 rounded-lg bg-green-100 text-green-700 text-sm font-medium hover:bg-green-200"
                                    >
                                        📞 Appeler
                                    </a>
                                @endif

                                @if($rdv->adresse || $rdv->ville)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($rdv->adresse ?? '') . ' ' . ($rdv->ville ?? '')) }}"
                                        target="_blank"
                                        class="px-3 py-2 rounded-lg bg-blue-100 text-blue-700 text-sm font-medium hover:bg-blue-200"
                                    >
                                        📍 GPS
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 italic py-6">
                            Aucune mission aujourd’hui.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow border p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">🛠️ Gestion complète des missions</h3>
                <livewire:employe.mes-rendez-vous />
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow border p-4">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">🧾 Historique récent</h3>

                <div class="space-y-3">
                    @forelse($historiqueRecent as $rdv)
                        <div class="border rounded-xl p-3 bg-gray-50">
                            <p class="font-medium text-gray-800">
                                {{ ucfirst(str_replace('_', ' ', $rdv->service_type ?? 'Service')) }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $rdv->date }} à {{ $rdv->heure }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $rdv->client->name ?? 'Client' }}
                            </p>
                            @if($rdv->duree_reelle)
                                <p class="text-xs text-gray-500 mt-1">
                                    Durée réelle : {{ $rdv->duree_reelle }} min
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 italic">
                            Aucun historique récent.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow border">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">🛠️ Mes limites de RDV par jour</h3>

                <div class="space-y-2">
                    @foreach(\Carbon\Carbon::now()->startOfWeek()->daysUntil(\Carbon\Carbon::now()->endOfWeek()) as $jour)
                        <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                            <div class="text-sm text-gray-700 font-medium w-1/3">
                                {{ $jour->translatedFormat('l d F') }}
                            </div>
                            <div class="w-2/3">
                                @livewire('modifier-limite-jour', [
                                    'date' => $jour->format('Y-m-d'),
                                    'user_id' => auth()->id(),
                                    'fromAdmin' => false
                                ], key($jour->format('Ymd') . '-' . auth()->id()))
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <livewire:feedbacks-employe />
            <livewire:employe.feedback-stats />
            <livewire:employe.validation-multiple-rdv />
        </div>
    </div>
</div>