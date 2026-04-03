<div class="p-6 space-y-6">

    <x-active-sessions />

    <h2 class="text-2xl font-bold text-blue-900">🛡️ Tableau de bord administrateur</h2>

    <x-toast />

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded shadow border">
            <p class="text-sm text-gray-500">En attente</p>
            <p class="text-2xl font-bold text-amber-600">{{ $adminKpis['en_attente'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow border">
            <p class="text-sm text-gray-500">Urgences vieillissantes</p>
            <p class="text-2xl font-bold text-red-600">{{ $adminKpis['urgentes_vieilles'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow border">
            <p class="text-sm text-gray-500">Missions longues</p>
            <p class="text-2xl font-bold text-orange-600">{{ $adminKpis['missions_longues'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow border">
            <p class="text-sm text-gray-500">Employés surchargés</p>
            <p class="text-2xl font-bold text-rose-600">{{ $adminKpis['employes_surcharges'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow border">
            <p class="text-sm text-gray-500">Missions du jour</p>
            <p class="text-2xl font-bold text-blue-700">{{ $adminKpis['missions_du_jour'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow border">
            <p class="text-sm text-gray-500">Terminées ce mois</p>
            <p class="text-2xl font-bold text-emerald-700">{{ $adminKpis['missions_terminees_mois'] }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm text-slate-500">Clients Premium actifs</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $premiumClientsCount }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm text-slate-500">Clients Standard</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $standardClientsCount }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm text-slate-500">Abonnements actifs</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $activeSubscriptionsCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Rendez-vous sans employé attribué</h3>
                <p class="text-sm text-slate-500">Demandes standard à traiter rapidement</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($rendezVousSansEmploye as $rdv)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="font-semibold text-slate-900">
                        {{ $rdv->client->name ?? 'Client' }} — {{ ucfirst(str_replace('_', ' ', $rdv->service_type ?? 'Service')) }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ $rdv->date }} à {{ substr((string) $rdv->heure, 0, 5) }}
                    </p>
                    <p class="text-sm text-slate-500">
                        {{ $rdv->adresse ?? 'Adresse non précisée' }}, {{ $rdv->ville ?? '—' }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <x-badge :status="$rdv->status" />
                    <x-priority-badge :priority="$rdv->priorite" />
                </div>
            </div>
            @empty
            <div class="text-sm text-slate-500 italic">
                Aucun rendez-vous sans employé attribué.
            </div>
            @endforelse
        </div>
    </div>


    <div class="bg-white rounded-3xl border border-amber-200 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Rendez-vous Premium</h3>
                <p class="text-sm text-slate-500">Demandes clients premium et suivi personnalisé</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($premiumRendezVous as $rdv)
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="font-semibold text-slate-900">
                        ★ {{ $rdv->client->name ?? 'Client Premium' }}
                    </p>
                    <p class="text-sm text-slate-600 mt-1">
                        {{ ucfirst(str_replace('_', ' ', $rdv->service_type ?? 'Service')) }} — {{ $rdv->date }} à {{ substr((string) $rdv->heure, 0, 5) }}
                    </p>
                    <p class="text-sm text-slate-500">
                        Employé : {{ $rdv->employe->name ?? 'À confirmer' }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <x-badge :status="$rdv->status" />
                    <x-priority-badge :priority="$rdv->priorite" />
                </div>
            </div>
            @empty
            <div class="text-sm text-slate-500 italic">
                Aucun rendez-vous premium à venir.
            </div>
            @endforelse
        </div>
    </div>


    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Clients Premium actifs</h3>
                <p class="text-sm text-slate-500">Suivi des clients à forte valeur</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($premiumClients as $client)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-slate-900">{{ $client->name }}</p>
                    <p class="text-sm text-slate-500">{{ $client->email }}</p>
                </div>

                <div class="text-right">
                    <span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-semibold text-amber-700">
                        Premium actif
                    </span>
                </div>
            </div>
            @empty
            <div class="text-sm text-slate-500 italic">
                Aucun client premium actif.
            </div>
            @endforelse
        </div>
    </div>


    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Premium sans employés favoris</h3>
                <p class="text-sm text-slate-500">Clients premium à accompagner pour mieux personnaliser leur expérience</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($premiumClientsWithoutFavorites as $client)
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="font-semibold text-slate-900">{{ $client->name }}</p>
                <p class="text-sm text-slate-500">{{ $client->email }}</p>
            </div>
            @empty
            <div class="text-sm text-slate-500 italic">
                Tous les clients premium ont déjà au moins un employé favori.
            </div>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded shadow border">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-red-700">🚨 Urgences trop anciennes</h3>
                    <p class="text-sm text-gray-500">Demandes urgentes encore bloquées en attente.</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($urgencesVieillissantes as $rdv)
                <x-rdv-cleaning-card :rdv="$rdv">
                    <div class="pt-2 flex flex-wrap gap-3">
                        <button wire:click="ouvrirMission({{ $rdv->id }})" class="text-sm text-blue-600 underline">
                            👁️ Voir détail
                        </button>
                        <button wire:click="ouvrirPlanning({{ $rdv->id }})" class="text-sm text-amber-700 underline">
                            🗓️ Replanifier
                        </button>
                    </div>
                </x-rdv-cleaning-card>
                @empty
                <div class="text-sm text-gray-500 italic">
                    Aucune urgence en attente prolongée.
                </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-5 rounded shadow border">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-orange-700">⏱️ Services sous-estimés</h3>
                    <p class="text-sm text-gray-500">Services qui dépassent régulièrement la durée prévue.</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($servicesSousEstimes as $service => $row)
                <div class="border rounded-lg p-4 bg-gray-50">
                    <p class="font-semibold text-gray-800">
                        {{ ucfirst(str_replace('_', ' ', $service)) }}
                    </p>
                    <p class="text-sm text-gray-600">
                        Écart moyen : +{{ $row['avg_gap'] }} min
                    </p>
                    <p class="text-sm text-gray-600">
                        Base : {{ $row['count'] }} mission(s)
                    </p>
                </div>
                @empty
                <div class="text-sm text-gray-500 italic">
                    Aucun service critique détecté.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow border">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">📤 Exporter les feedbacks (PDF)</h3>

        <form action="{{ route('admin.feedbacks.export') }}" method="GET" target="_blank" class="space-y-3 md:flex md:items-end md:gap-4">
            <div class="flex flex-col">
                <label for="export_employe_id" class="text-sm text-gray-600">Employé :</label>
                <select name="employe_id" id="export_employe_id" class="border rounded px-2 py-1 text-sm">
                    <option value="">— Tous —</option>
                    @foreach($employes as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label for="client_id" class="text-sm text-gray-600">Client :</label>
                <select name="client_id" id="client_id" class="border rounded px-2 py-1 text-sm">
                    <option value="">— Tous —</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 transition">
                    📄 Télécharger le PDF
                </button>
            </div>
        </form>
    </div>

    <livewire:admin.feedback-stats />

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded shadow border">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">📍 Interventions du jour</h3>

            <div class="space-y-4">
                @forelse($interventionsDuJour as $rdv)
                <x-rdv-cleaning-card :rdv="$rdv">
                    <div class="pt-2 flex flex-wrap gap-3">
                        <button
                            wire:click="ouvrirMission({{ $rdv->id }})"
                            class="text-sm text-blue-600 underline">
                            👁️ Voir détail
                        </button>

                        @if(in_array($rdv->status, ['en_attente', 'confirme', 'en_route', 'sur_place']))
                        <button
                            wire:click="ouvrirPlanning({{ $rdv->id }})"
                            class="text-sm text-amber-700 underline">
                            🗓️ Replanifier
                        </button>
                        @endif
                    </div>
                </x-rdv-cleaning-card>
                @empty
                <div class="text-sm text-gray-500 italic">
                    Aucune intervention prévue aujourd’hui.
                </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-5 rounded shadow border">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">📊 Charge des employés aujourd’hui</h3>

            <div class="space-y-3">
                @forelse($chargeEmployes as $item)
                <div class="border rounded-lg p-3 bg-gray-50 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $item['employe']->name }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $item['count'] }} intervention(s) • {{ $item['minutes'] }} min • {{ $item['hours'] }} h
                        </p>
                    </div>

                    <div>
                        @if($item['minutes'] >= 480)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold bg-red-100 text-red-700 border-red-200">
                            Surchargé
                        </span>
                        @elseif($item['minutes'] >= 300)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold bg-orange-100 text-orange-700 border-orange-200">
                            Chargé
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold bg-green-100 text-green-700 border-green-200">
                            OK
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-sm text-gray-500 italic">
                    Aucun employé trouvé.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white p-5 rounded shadow border">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-red-700">🚨 Interventions urgentes</h3>
                <p class="text-sm text-gray-500">Demandes prioritaires à traiter rapidement.</p>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($urgences as $rdv)
            <x-rdv-cleaning-card :rdv="$rdv">
                <div class="pt-2 flex flex-wrap gap-3">
                    <button
                        wire:click="ouvrirMission({{ $rdv->id }})"
                        class="text-sm text-blue-600 underline">
                        👁️ Voir détail
                    </button>

                    @if(in_array($rdv->status, ['en_attente', 'confirme', 'en_route', 'sur_place']))
                    <button
                        wire:click="ouvrirPlanning({{ $rdv->id }})"
                        class="text-sm text-amber-700 underline">
                        🗓️ Replanifier
                    </button>
                    @endif
                </div>
            </x-rdv-cleaning-card>
            @empty
            <div class="text-sm text-gray-500 italic">
                Aucune intervention urgente pour le moment.
            </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white p-5 rounded shadow border">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-emerald-700">✅ Missions terminées récemment</h3>
                <p class="text-sm text-gray-500">Contrôle rapide des dernières interventions clôturées.</p>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($missionsTerminees as $rdv)
            <x-rdv-cleaning-card :rdv="$rdv">
                <div class="pt-2 flex flex-wrap gap-3">
                    <button
                        wire:click="ouvrirMission({{ $rdv->id }})"
                        class="text-sm text-blue-600 underline">
                        👁️ Voir détail
                    </button>

                    @if(in_array($rdv->status, ['en_attente', 'confirme', 'en_route', 'sur_place']))
                    <button
                        wire:click="ouvrirPlanning({{ $rdv->id }})"
                        class="text-sm text-amber-700 underline">
                        🗓️ Replanifier
                    </button>
                    @endif
                </div>
            </x-rdv-cleaning-card>
            @empty
            <div class="text-sm text-gray-500 italic">
                Aucune mission terminée récemment.
            </div>
            @endforelse
        </div>

        <div class="bg-white p-5 rounded shadow border">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">🧪 Suivi qualité des missions</h3>
                    <p class="text-sm text-gray-500">Contrôle des rapports, photos après intervention et écarts de durée.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                <div class="border rounded-lg p-4 bg-gray-50">
                    <p class="text-sm text-gray-500">Missions sans rapport</p>
                    <p class="text-2xl font-bold text-red-600">{{ $qualiteStats['sans_rapport'] }}</p>
                </div>

                <div class="border rounded-lg p-4 bg-gray-50">
                    <p class="text-sm text-gray-500">Missions sans photos après</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $qualiteStats['sans_photos_apres'] }}</p>
                </div>

                <div class="border rounded-lg p-4 bg-gray-50">
                    <p class="text-sm text-gray-500">Missions avec durée réelle</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $qualiteStats['avec_duree_reelle'] }}</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($qualiteMissions as $item)
                @php
                $rdv = $item['rdv'];
                @endphp

                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ ucfirst(str_replace('_', ' ', $rdv->service_type ?? 'Service')) }}
                            </p>
                            <p class="text-sm text-gray-600">
                                👤 {{ $rdv->client->name ?? '—' }} · 🧑‍💼 {{ $rdv->employe->name ?? '—' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                📅 {{ $rdv->date }} à {{ $rdv->heure }}
                            </p>
                            @if(!empty($suggestedEmployees))
                            <div class="mt-4">
                                <h4 class="text-sm font-semibold text-slate-800 mb-2">🧠 Suggestions automatiques</h4>

                                <div class="space-y-2">
                                    @foreach($suggestedEmployees as $suggestion)
                                    <div class="flex items-center justify-between border rounded-lg p-3 bg-gray-50">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $suggestion['name'] }}</p>
                                            <p class="text-xs text-gray-600">
                                                {{ $suggestion['rdv_count'] }} mission(s) • {{ $suggestion['load_minutes'] }} min planifiées
                                                @if($suggestion['same_city_bonus'] < 0)
                                                    • même ville
                                                    @endif
                                                    </p>
                                        </div>

                                        <button
                                            wire:click="appliquerSuggestionEmploye({{ $suggestion['id'] }})"
                                            type="button"
                                            class="text-sm px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">
                                            Choisir
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <x-badge :status="$rdv->status" />
                            <x-priority-badge :priority="$rdv->priorite" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4 text-sm">
                        <div class="bg-white border rounded p-3">
                            <p class="text-gray-500">Rapport</p>
                            <p class="font-semibold {{ $item['has_report'] ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ $item['has_report'] ? 'Présent' : 'Manquant' }}
                            </p>
                        </div>

                        <div class="bg-white border rounded p-3">
                            <p class="text-gray-500">Photos après</p>
                            <p class="font-semibold {{ $item['has_after_photos'] ? 'text-emerald-700' : 'text-orange-600' }}">
                                {{ $item['has_after_photos'] ? 'Présentes' : 'Manquantes' }}
                            </p>
                        </div>

                        <div class="bg-white border rounded p-3">
                            <p class="text-gray-500">Durée</p>
                            <p class="font-semibold text-slate-800">
                                Estimée : {{ $item['estimated'] ? $item['estimated'] . ' min' : '—' }}
                                <br>
                                Réelle : {{ $item['real'] ? $item['real'] . ' min' : '—' }}
                            </p>
                        </div>
                    </div>

                    @if(!is_null($item['difference']))
                    <div class="mt-3 text-sm">
                        @if($item['is_long_overrun'])
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold bg-red-100 text-red-700 border-red-200">
                            +{{ $item['difference'] }} min par rapport à l’estimé
                        </span>
                        @elseif($item['is_short_underrun'])
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold bg-blue-100 text-blue-700 border-blue-200">
                            {{ $item['difference'] }} min par rapport à l’estimé
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold bg-emerald-100 text-emerald-700 border-emerald-200">
                            Durée cohérente
                        </span>
                        @endif
                    </div>
                    @endif

                    @if($rdv->commentaire_fin_mission)
                    <div class="mt-3 bg-white border rounded p-3 text-sm text-gray-700">
                        <span class="font-medium">Rapport employé :</span>
                        <p class="mt-1">{{ $rdv->commentaire_fin_mission }}</p>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-sm text-gray-500 italic">
                    Aucune donnée qualité disponible pour le moment.
                </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-5 rounded shadow border">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">🕓 Journal d’activité admin</h3>
                    <p class="text-sm text-gray-500">Historique récent des actions sensibles réalisées dans l’outil.</p>
                </div>
            </div>
            <div class="bg-white p-5 rounded shadow border space-y-5">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">📈 Analytics métier avancées</h3>
                    <p class="text-sm text-gray-500">Vue business sur les services, les villes, les durées et la performance.</p>
                </div>

                <div class="bg-white p-5 rounded shadow border space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">🧠 Recommandations automatiques</h3>
                        <p class="text-sm text-gray-500">Aide à la décision basée sur la charge, les durées, les zones et les retours clients.</p>
                    </div>

                    <div class="space-y-3">
                        @forelse($recommendations as $rec)
                        @php
                        $classes = match($rec['level']) {
                        'danger' => 'bg-red-50 border-red-200 text-red-800',
                        'warning' => 'bg-orange-50 border-orange-200 text-orange-800',
                        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
                        default => 'bg-gray-50 border-gray-200 text-gray-800',
                        };
                        @endphp

                        <div class="border rounded-lg p-4 {{ $classes }}">
                            <p class="font-semibold">{{ $rec['title'] }}</p>
                            <p class="text-sm mt-1">{{ $rec['message'] }}</p>
                        </div>
                        @empty
                        <div class="text-sm text-gray-500 italic">
                            Aucune recommandation particulière pour le moment.
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="text-sm text-gray-500">Feedback reçu</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $feedbackRate }}%</p>
                    </div>

                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="text-sm text-gray-500">Durée estimée moyenne</p>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ $dureeStats['avg_estimated'] ? $dureeStats['avg_estimated'] . ' min' : '—' }}
                        </p>
                    </div>

                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="text-sm text-gray-500">Durée réelle moyenne</p>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ $dureeStats['avg_real'] ? $dureeStats['avg_real'] . ' min' : '—' }}
                        </p>
                    </div>

                    <div class="border rounded-lg p-4 bg-gray-50">
                        <p class="text-sm text-gray-500">Écart moyen</p>
                        <p class="text-2xl font-bold {{ ($dureeStats['avg_gap'] ?? 0) > 0 ? 'text-red-600' : 'text-emerald-700' }}">
                            @if(!is_null($dureeStats['avg_gap']))
                            {{ $dureeStats['avg_gap'] > 0 ? '+' : '' }}{{ $dureeStats['avg_gap'] }} min
                            @else
                            —
                            @endif
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="border rounded-xl p-4 bg-gray-50">
                        <h4 class="font-semibold text-slate-800 mb-3">🧼 Services les plus demandés</h4>
                        <div class="space-y-2">
                            @forelse($topServices as $service)
                            <div class="flex items-center justify-between border rounded p-2 bg-white">
                                <span class="text-sm text-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $service->service_type ?? 'Service')) }}
                                </span>
                                <span class="text-sm font-semibold text-slate-800">{{ $service->total }}</span>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">Aucune donnée disponible.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="border rounded-xl p-4 bg-gray-50">
                        <h4 class="font-semibold text-slate-800 mb-3">📍 Villes les plus demandées</h4>
                        <div class="space-y-2">
                            @forelse($topVilles as $ville)
                            <div class="flex items-center justify-between border rounded p-2 bg-white">
                                <span class="text-sm text-gray-700">{{ $ville->ville }}</span>
                                <span class="text-sm font-semibold text-slate-800">{{ $ville->total }}</span>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">Aucune donnée disponible.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="border rounded-xl p-4 bg-gray-50">
                        <h4 class="font-semibold text-slate-800 mb-3">🧑‍💼 Performance employés</h4>
                        <div class="space-y-2">
                            @forelse($performanceEmployes as $item)
                            <div class="border rounded p-3 bg-white">
                                <p class="font-semibold text-gray-800">{{ $item['employe']->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $item['missions_terminees'] }} mission(s) terminée(s)
                                </p>
                                <p class="text-sm text-gray-600">
                                    Écart moyen :
                                    {{ !is_null($item['avg_gap']) ? ($item['avg_gap'] > 0 ? '+' : '') . $item['avg_gap'] . ' min' : '—' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Note moyenne :
                                    {{ !is_null($item['avg_note']) ? $item['avg_note'] . '/5' : '—' }}
                                </p>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500 italic">Aucune donnée disponible.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($recentActivityLogs as $log)
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div>
                            <p class="font-semibold text-gray-800">
                                @php
                                $actionLabel = match($log->action) {
                                'mission_replanifiee' => 'Mission replanifiée',
                                'mission_statut_modifie' => 'Statut de mission modifié',
                                'mission_terminee_avec_rapport' => 'Mission terminée avec rapport',
                                'rdv_modifie_par_client' => 'Rendez-vous modifié par le client',
                                'rdv_annule_par_client' => 'Rendez-vous annulé par le client',
                                'feedback_repondu_par_admin' => 'Réponse admin à un feedback',
                                'export_rendez_vous' => 'Export des rendez-vous',
                                'export_feedbacks' => 'Export des feedbacks',
                                'import_csv_execute' => 'Import CSV exécuté',
                                'import_csv_avec_erreurs' => 'Import CSV avec erreurs',
                                default => ucfirst(str_replace('_', ' ', $log->action)),
                                'rappel_24h_envoye' => 'Rappel 24h envoyé',
                                'rappel_2h_envoye' => 'Rappel 2h envoyé',
                                'demande_feedback_envoyee' => 'Demande de feedback envoyée',
                                'alerte_urgence_envoyee' => 'Alerte urgence envoyée',
                                'alerte_depassement_durees' => 'Alerte sur dépassements de durée',
                                'alerte_taux_feedback_faible' => 'Alerte taux de feedback faible',
                                'suggestion_reaffectation_auto' => 'Suggestion automatique de réaffectation',
                                };
                                @endphp

                                {{ $actionLabel }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Par {{ $log->user->name ?? 'Système automatique' }} • {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <div class="text-xs text-gray-500">
                            @if($log->target_id)
                            Cible #{{ $log->target_id }}
                            @endif
                        </div>
                    </div>

                    @if(!empty($log->meta))
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        @foreach($log->meta as $key => $value)
                        <div class="bg-white border rounded p-2">
                            <span class="font-medium text-gray-700">
                                {{ ucfirst(str_replace('_', ' ', $key)) }} :
                            </span>
                            <span class="text-gray-600">
                                {{ is_array($value) ? json_encode($value) : $value }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-sm text-gray-500 italic">
                    Aucune activité enregistrée pour le moment.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white p-5 rounded shadow">
        <h2 class="text-lg font-semibold text-blue-900 mb-4">🧩 Limites journalières des employés</h2>

        <div class="mb-4">
            <label for="dashboard_employe_id" class="text-sm font-medium text-gray-700">Choisir un employé :</label>
            <select wire:model="employeSelectionne" id="dashboard_employe_id" class="mt-1 block w-64 border-gray-300 rounded shadow-sm text-sm">
                <option value="">-- Sélectionner --</option>
                @foreach($employes as $emp)
                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>

        @if($employeSelectionne)
        <div class="space-y-2">
            @foreach(\Carbon\Carbon::now()->startOfWeek()->daysUntil(\Carbon\Carbon::now()->endOfWeek()) as $jour)
            <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                <div class="text-sm text-gray-700 font-medium w-1/3">
                    {{ $jour->translatedFormat('l d F') }}
                </div>
                <div class="w-2/3">
                    @livewire('modifier-limite-jour', [
                    'date' => $jour->format('Y-m-d'),
                    'user_id' => $employeSelectionne,
                    'fromAdmin' => true
                    ], key($jour->format('Ymd') . '-' . $employeSelectionne))
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div id="chartStats" class="bg-white rounded shadow p-4"></div>
        <div id="chartMensuel" class="bg-white rounded shadow p-4"></div>
    </div>

    <div class="bg-white rounded shadow mt-6 p-4">
        <h3 class="text-lg font-semibold mb-2">📆 Calendrier global</h3>
        <div id="fullcalendar-admin"></div>
    </div>

    <livewire:admin-feedbacks />
    <livewire:admin.gestion-utilisateurs />
    <livewire:admin.agenda-hebdomadaire />
    <livewire:notifications />
    <x-admin.recapitulatif-acces />
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
    let chartInstance = null;
    let chartMensuelInstance = null;
    let adminCalendarInstance = null;
    let livewireListenersRegistered = false;

    function initAdminCharts() {
        const chartStatsEl = document.querySelector('#chartStats');
        const chartMensuelEl = document.querySelector('#chartMensuel');

        if (!chartStatsEl || !chartMensuelEl) return;

        if (chartInstance) chartInstance.destroy();
        if (chartMensuelInstance) chartMensuelInstance.destroy();

        chartInstance = new ApexCharts(chartStatsEl, {
            chart: {
                type: 'donut',
                height: 300
            },
            series: [0, 0, 0],
            labels: ['Confirmés', 'En attente', 'Refusés'],
            colors: ['#16a34a', '#eab308', '#dc2626']
        });

        chartMensuelInstance = new ApexCharts(chartMensuelEl, {
            chart: {
                type: 'line',
                height: 300
            },
            series: [{
                name: 'RDV',
                data: Array(12).fill(0)
            }],
            xaxis: {
                categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
            },
            colors: ['#3b82f6']
        });

        chartInstance.render();
        chartMensuelInstance.render();
    }

    function initAdminCalendar() {
        const calendarEl = document.getElementById('fullcalendar-admin');
        if (!calendarEl) return;

        if (adminCalendarInstance) {
            adminCalendarInstance.destroy();
        }

        adminCalendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            events: @js($rdvs),
            eventClick(info) {
                alert('RDV avec ' + info.event.title);
            }
        });

        adminCalendarInstance.render();
    }

    function registerAdminDashboardListeners() {
        if (livewireListenersRegistered) return;
        livewireListenersRegistered = true;

        Livewire.on('updateChartData', (event) => {
            const data = event?.data ?? event;
            if (!chartInstance) return;

            chartInstance.updateSeries([
                data.confirme || 0,
                data.attente || 0,
                data.refuse || 0
            ]);
        });

        Livewire.on('updateMonthlyChart', (event) => {
            const data = event?.data ?? event;
            if (!chartMensuelInstance) return;

            chartMensuelInstance.updateSeries([{
                name: 'RDV',
                data: data
            }]);
        });
    }

    function bootAdminDashboard() {
        initAdminCharts();
        initAdminCalendar();
        registerAdminDashboardListeners();
    }

    document.addEventListener('livewire:load', bootAdminDashboard);
    document.addEventListener('livewire:navigated', bootAdminDashboard);
</script>
@endpush