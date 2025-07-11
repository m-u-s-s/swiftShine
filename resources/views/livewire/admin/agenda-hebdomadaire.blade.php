<div class="bg-white p-4 rounded shadow space-y-4">

    <h3 class="text-xl font-semibold text-blue-800">🗓️ Vue hebdomadaire des rendez-vous</h3>

    <div class="flex items-center justify-between">
        <button wire:click="semainePrecedente" class="text-sm text-blue-600 hover:underline">⬅️ Semaine -</button>
        <div class="text-sm font-semibold text-gray-700">
            Semaine du {{ \Carbon\Carbon::parse($semaine)->translatedFormat('d M') }}
        </div>
        <button wire:click="semaineSuivante" class="text-sm text-blue-600 hover:underline">Semaine + ➡️</button>
    </div>

    <div class="flex flex-wrap items-center gap-4">
        <label class="text-sm">Filtrer par employé :</label>
        <select wire:model="employe_id" class="border rounded px-2 py-1 text-sm">
            <option value="">— Tous —</option>
            @foreach($employes as $e)
                <option value="{{ $e->id }}">{{ $e->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($jours as $jour)
            <div class="border rounded p-3">
                <div class="font-semibold text-blue-900 mb-2">{{ $jour['label'] }}</div>

                @forelse($jour['rdvs'] as $rdv)
                    @php
                        $badge = match($rdv->status) {
                            'valide' => 'bg-green-100 text-green-700',
                            'refuse' => 'bg-red-100 text-red-700',
                            default => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp

                    <div class="mb-2 text-sm border-b pb-1">
                        <div class="flex justify-between">
                            <span>🕒 {{ $rdv->heure }}</span>
                            <span class="text-xs px-2 py-1 rounded {{ $badge }}">
                                {{ ucfirst($rdv->status) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-600">
                            👤 {{ $rdv->client->name ?? '—' }}<br>
                            🧑‍💼 {{ $rdv->employe->name ?? '—' }}
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-gray-500 italic">Aucun RDV</div>
                @endforelse
            </div>
        @endforeach
    </div>
</div>
