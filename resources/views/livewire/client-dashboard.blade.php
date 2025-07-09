<div class="p-6 space-y-6">

    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
    <div class="bg-white p-4 rounded shadow space-y-2 mt-4">
        <h3 class="text-sm font-semibold text-blue-800">🔐 Connexions actives</h3>

        @foreach ($sessions = Auth::user()->sessions ?? [] as $session)
        <div class="flex items-center justify-between text-sm border-b py-2">
            <div>
                {{ $session->agent['platform'] ?? 'Inconnu' }} -
                {{ $session->agent['browser'] ?? 'Navigateur inconnu' }}
                <br>
                <span class="text-xs text-gray-500">
                    {{ $session->ip_address }},
                    dernière activité : {{ \Carbon\Carbon::parse($session->last_active)->diffForHumans() }}
                </span>
            </div>
            @if ($session->is_current_device)
            <span class="text-green-600 text-xs font-semibold">Appareil actuel</span>
            @endif
        </div>
        @endforeach
    </div>
    @endif
    <h2 class="text-2xl font-bold text-blue-900">🎯 Tableau de bord client</h2>

    {{-- ✅ Toast global --}}
    <x-toast />

    {{-- 📊 Statistiques --}}
    <div class="bg-white p-4 rounded shadow border">
        <h3 class="text-lg font-semibold mb-2">Mes statistiques</h3>
        <p class="text-sm text-gray-700">Vous avez au total <strong>{{ $total }}</strong> rendez-vous enregistrés.</p>
    </div>

    {{-- 📅 Rendez-vous à venir --}}
    <div class="bg-white p-4 rounded shadow border">
        <h3 class="text-lg font-semibold mb-4">📅 À venir</h3>
        <table class="w-full text-sm border">
            <thead class="bg-blue-50">
                <tr>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Heure</th>
                    <th class="p-2 border">Statut</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($avenir as $rdv)
                <tr>
                    <td class="p-2 border">{{ $rdv->date }}</td>
                    <td class="p-2 border">{{ $rdv->heure }}</td>
                    <td class="p-2 border"><x-badge :status="$rdv->status" /></td>
                    <td class="p-2 border space-x-2">
                        <button wire:click="modifier({{ $rdv->id }})" class="text-sm text-blue-600 underline">✏️ Modifier</button>
                        <button onclick="if(confirm('Confirmer l\'annulation ?')) @this.annuler({{ $rdv->id }})"
                            class="text-sm text-red-600 underline">❌ Annuler</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center italic text-gray-500 py-3">Aucun rendez-vous à venir.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $avenir->links() }}</div>
    </div>

    {{-- ✏️ Modale d’édition --}}
    @if($editRdvId)
    <div class="bg-yellow-50 p-4 border border-yellow-300 rounded shadow">
        <h4 class="font-semibold text-yellow-800 mb-2">✏️ Modifier le rendez-vous</h4>
        <div class="flex gap-4 items-end">
            <div>
                <label class="text-sm text-gray-700">Date</label>
                <input type="date" wire:model="editDate" class="text-sm border-gray-300 rounded px-2 py-1">
            </div>
            <div>
                <label class="text-sm text-gray-700">Heure</label>
                <input type="time" wire:model="editHeure" class="text-sm border-gray-300 rounded px-2 py-1">
            </div>
            <button wire:click="enregistrerModif"
                class="bg-blue-600 text-white px-3 py-1 rounded text-sm">💾 Sauvegarder</button>
            <button wire:click="$reset"
                class="text-sm text-gray-600 underline">Annuler</button>
        </div>
    </div>
    @endif

    {{-- 📜 Historique --}}
    <div class="bg-white p-4 rounded shadow border">
        <h3 class="text-lg font-semibold mb-2">📜 Derniers rendez-vous passés</h3>
        <ul class="list-disc list-inside text-sm text-gray-700">
            @forelse($passe as $rdv)
            <li>{{ $rdv->date }} à {{ $rdv->heure }} &mdash; <x-badge :status="$rdv->status" /></li>
            @empty
            <li class="italic text-gray-500">Aucun rendez-vous passé enregistré.</li>
            @endforelse
        </ul>
    </div>
</div>

<livewire:feedbacks-client />