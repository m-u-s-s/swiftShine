<div class="bg-white p-4 rounded shadow space-y-4">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-xl font-semibold text-blue-800">🗓️ Vue hebdomadaire des interventions</h3>
            <p class="text-sm text-gray-500">Suivi global des prestations de nettoyage prévues cette semaine.</p>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="semainePrecedente" class="text-sm text-blue-600 hover:underline">
                ⬅️ Semaine -
            </button>

            <div class="text-sm font-semibold text-gray-700">
                Semaine du {{ \Carbon\Carbon::parse($semaine)->translatedFormat('d M') }}
            </div>

            <button wire:click="semaineSuivante" class="text-sm text-blue-600 hover:underline">
                Semaine + ➡️
            </button>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-4">
        <div>
            <label class="text-sm text-gray-700 font-medium">Employé :</label>
            <select wire:model.live="employe_id" class="border rounded px-2 py-1 text-sm">
                <option value="">— Tous —</option>
                @foreach($employes as $e)
                    <option value="{{ $e->id }}">{{ $e->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-700 font-medium">Priorité :</label>
            <select wire:model.live="priorite" class="border rounded px-2 py-1 text-sm">
                <option value="">— Toutes —</option>
                <option value="normale">Normale</option>
                <option value="haute">Haute</option>
                <option value="urgente">Urgente</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($jours as $jour)
            <div class="border rounded-xl p-4 bg-gray-50">
                <div class="mb-3">
                    <div class="font-semibold text-blue-900 text-base">
                        {{ $jour['label'] }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $jour['rdvs']->count() }} intervention(s) • {{ $jour['total_minutes'] }} min • {{ $jour['total_hours'] }} h
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($jour['rdvs'] as $rdv)
                        <x-rdv-cleaning-card :rdv="$rdv" />
                    @empty
                        <div class="text-sm text-gray-500 italic">
                            Aucune intervention prévue.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>