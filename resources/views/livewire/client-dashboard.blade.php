<div class="p-4 md:p-6 space-y-6">
    <x-active-sessions />
    <x-toast />

    {{-- Header --}}
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900">
                    Bonjour {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}
                </h2>

                @if($isPremium)
                <span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-semibold text-amber-700">
                    ★ Premium
                </span>
                @else
                <span class="inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                    Standard
                </span>
                @endif
            </div>

            <p class="text-sm text-slate-500 mt-2">
                @if($isPremium)
                Profitez de vos avantages premium et d’une expérience plus personnalisée.
                @else
                Gérez facilement vos prestations, votre historique et vos prochaines interventions.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('client.rendezvous.create') }}"
                class="inline-flex items-center px-4 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 transition">
                ➕ Nouveau rendez-vous
            </a>

            <a href="{{ route('client.rendezvous.index') }}"
                class="inline-flex items-center px-4 py-2.5 rounded-xl border bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                📅 Mes rendez-vous
            </a>

            <a href="{{ route('client.historique') }}"
                class="inline-flex items-center px-4 py-2.5 rounded-xl border bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                🕘 Historique
            </a>

            @if($isPremium && count($favoriteEmployes))
            <a href="{{ route('client.rendezvous.create') }}"
                class="inline-flex items-center px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-sm font-semibold text-amber-700 hover:bg-amber-100 transition">
                ★ Réserver avec un favori
            </a>
            @endif
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Total prestations</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $statsClient['total'] }}</p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">À venir</p>
            <p class="text-2xl font-bold text-sky-700 mt-1">{{ $statsClient['avenir'] }}</p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Terminées</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $statsClient['termine'] }}</p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Feedbacks laissés</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $statsClient['feedbacks'] }}</p>
        </div>
    </div>

    {{-- Prochain rdv + abonnement / upgrade --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Votre priorité</p>
                        <h3 class="text-xl font-bold text-slate-900">Prochain rendez-vous</h3>
                    </div>

                    @if($prochainRendezVous)
                    <div class="flex items-center gap-2">
                        <x-badge :status="$prochainRendezVous->status" />
                        <x-priority-badge :priority="$prochainRendezVous->priorite" />
                    </div>
                    @endif
                </div>

                @if($prochainRendezVous)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-sm text-slate-500">Service</p>
                        <p class="text-lg font-bold text-slate-900 mt-1">
                            {{ ucfirst(str_replace('_', ' ', $prochainRendezVous->service_type ?? '—')) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-sm text-slate-500">Date & heure</p>
                        <p class="text-lg font-bold text-slate-900 mt-1">
                            {{ $prochainRendezVous->date }} à {{ substr((string) $prochainRendezVous->heure, 0, 5) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-sm text-slate-500">Employé</p>
                        <p class="text-lg font-bold text-slate-900 mt-1">
                            {{ $prochainRendezVous->employe->name ?? 'À confirmer par notre équipe' }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-sm text-slate-500">Adresse</p>
                        <p class="text-lg font-bold text-slate-900 mt-1">
                            {{ $prochainRendezVous->adresse ?? '—' }}, {{ $prochainRendezVous->ville ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('client.rendezvous.index') }}"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 transition">
                        Voir le détail
                    </a>

                    @if(!in_array($prochainRendezVous->status, ['en_route', 'sur_place', 'termine', 'refuse']))
                    <button type="button"
                        wire:click="modifier({{ $prochainRendezVous->id }})"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Modifier
                    </button>

                    <button type="button"
                        wire:click="annuler({{ $prochainRendezVous->id }})"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl border border-red-200 bg-red-50 text-sm font-semibold text-red-700 hover:bg-red-100 transition">
                        Annuler
                    </button>
                    @endif
                </div>
                @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                    <p class="text-slate-600 font-medium">Aucun rendez-vous à venir.</p>
                    <p class="text-sm text-slate-500 mt-1">Planifiez une nouvelle prestation en quelques clics.</p>

                    <a href="{{ route('client.rendezvous.create') }}"
                        class="inline-flex items-center mt-4 px-4 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 transition">
                        Réserver maintenant
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            @if($isPremium)
            <div class="bg-white rounded-3xl shadow-sm border border-amber-200 p-6">
                <p class="text-sm font-medium text-amber-700">Abonnement Premium</p>
                <h3 class="text-xl font-bold text-slate-900 mt-1">Plan actif</h3>

                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Statut</span>
                        <span class="font-semibold text-emerald-700">Actif</span>
                    </div>

                    @if($activeSubscription?->renewal_at)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Renouvellement</span>
                        <span class="font-semibold text-slate-800">
                            {{ optional($activeSubscription->renewal_at)->format('d/m/Y') }}
                        </span>
                    </div>
                    @elseif(auth()->user()->premium_renewal_at)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Renouvellement</span>
                        <span class="font-semibold text-slate-800">
                            {{ optional(auth()->user()->premium_renewal_at)->format('d/m/Y') }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="mt-5 rounded-2xl bg-amber-50 border border-amber-100 p-4">
                    <p class="text-sm font-semibold text-amber-800">Vos avantages</p>
                    <ul class="mt-2 space-y-2 text-sm text-amber-700">
                        <li>• Choix des employés favoris</li>
                        <li>• Visibilité sur les disponibilités</li>
                        <li>• Expérience plus personnalisée</li>
                    </ul>
                </div>
            </div>
            @else
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                <p class="text-sm font-medium text-slate-500">Passez au niveau supérieur</p>
                <h3 class="text-xl font-bold text-slate-900 mt-1">Offre Premium mensuelle</h3>

                <ul class="mt-4 space-y-2 text-sm text-slate-600">
                    <li>• Choisissez vos employés favoris</li>
                    <li>• Consultez leurs disponibilités</li>
                    <li>• Réservez avec une expérience plus personnalisée</li>
                </ul>

                <a href="{{ route('premium.offer') }}"
                    class="mt-5 inline-flex items-center px-4 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition">
                    Découvrir l’offre Premium
                </a>
            </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900">Adresses récentes</h3>

                <div class="mt-4 space-y-3">
                    @forelse($adressesRecentes as $adresse)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="font-semibold text-slate-800">{{ $adresse->adresse }}</p>
                        <p class="text-sm text-slate-500">{{ $adresse->ville ?? '—' }} {{ $adresse->code_postal ?? '' }}</p>
                    </div>
                    @empty
                    <div class="text-sm text-slate-500 italic">
                        Aucune adresse récente.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Favoris / rebooking --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-gradient-to-r from-slate-900 to-slate-700 text-white rounded-3xl shadow-sm p-6">
            <p class="text-sm text-slate-300">Réservation rapide</p>
            <h3 class="text-xl font-bold mt-1">Même service que la dernière fois</h3>

            @if($dernierRendezVous)
            <div class="mt-4 space-y-2 text-sm text-slate-200">
                <p><span class="font-semibold text-white">Service :</span> {{ ucfirst(str_replace('_', ' ', $dernierRendezVous->service_type ?? '—')) }}</p>
                <p><span class="font-semibold text-white">Adresse :</span> {{ $dernierRendezVous->adresse ?? '—' }}, {{ $dernierRendezVous->ville ?? '—' }}</p>
                <p><span class="font-semibold text-white">Type :</span> {{ ucfirst($dernierRendezVous->type_lieu ?? '—') }}</p>
                <p><span class="font-semibold text-white">Fréquence :</span> {{ ucfirst(str_replace('_', ' ', $dernierRendezVous->frequence ?? '—')) }}</p>
            </div>

            <div class="mt-5">
                <a href="{{ route('client.rendezvous.create') }}"
                    class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white text-slate-900 text-sm font-semibold hover:bg-slate-100 transition">
                    🔁 Reprendre une réservation similaire
                </a>
            </div>
            @else
            <p class="mt-4 text-sm text-slate-300">
                Votre dernière prestation apparaîtra ici pour faciliter vos prochaines réservations.
            </p>
            @endif
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-bold text-slate-900">Employés favoris</h3>
                @if($isPremium)
                <span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">
                    Premium
                </span>
                @endif
            </div>

            @if($isPremium)
            <div class="mt-4 space-y-3">
                @forelse($favoriteEmployes as $employe)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-800">{{ $employe->name }}</p>
                        <p class="text-sm text-slate-500">Employé favori</p>
                    </div>

                    <a href="{{ route('client.rendezvous.create') }}"
                        class="text-sm font-semibold text-sky-600 hover:text-sky-700">
                        Réserver
                    </a>
                </div>
                @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">
                    Aucun employé favori pour le moment.
                </div>
                @endforelse
            </div>
            @else
            <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4">
                <p class="text-sm font-medium text-slate-700">Disponible avec l’offre Premium</p>
                <p class="text-sm text-slate-500 mt-1">
                    En Premium, vous pouvez sélectionner vos employés favoris et réserver plus facilement avec eux.
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- Liste des prochains rdv --}}
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Mes prochaines interventions</h3>
                <p class="text-sm text-slate-500">Retrouvez vos prochains services planifiés.</p>
            </div>

            <a href="{{ route('client.rendezvous.index') }}"
                class="text-sm font-semibold text-sky-600 hover:text-sky-700">
                Voir tous mes rendez-vous
            </a>
        </div>

        <div class="space-y-4">
            @forelse($avenir as $rdv)
            <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-900 text-lg">
                            {{ ucfirst(str_replace('_', ' ', $rdv->service_type ?? 'Service non précisé')) }}
                        </p>
                        <p class="text-sm text-slate-600 mt-1">
                            📅 {{ $rdv->date }} à {{ substr((string) $rdv->heure, 0, 5) }}
                        </p>
                        <p class="text-sm text-slate-600">
                            📍 {{ $rdv->adresse ?? 'Adresse non précisée' }}, {{ $rdv->ville ?? '—' }}
                        </p>
                        <p class="text-sm text-slate-600">
                            🧑‍💼 {{ $rdv->employe->name ?? 'Employé à confirmer' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge :status="$rdv->status" />
                        <x-priority-badge :priority="$rdv->priorite" />
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center italic text-slate-500 py-4">
                Aucun rendez-vous à venir.
            </div>
            @endforelse
        </div>

        @if(method_exists($avenir, 'links'))
        <div class="mt-6">
            {{ $avenir->links() }}
        </div>
        @endif
    </div>
</div>