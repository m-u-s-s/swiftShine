<div class="p-6 bg-white rounded shadow-md">
    <h2 class="text-xl font-semibold text-blue-800 mb-4">📋 Mes rendez-vous</h2>

    {{-- ✅ TOAST animé --}}
    <x-toast />

    {{-- 🔍 Recherche + 🔃 Tri --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
        <div>
            <input type="text" wire:model.debounce.500ms="search"
                placeholder="Rechercher un client..."
                class="text-sm border-gray-300 rounded px-3 py-1 w-64"
            >
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Trier par date :</label>
            <select wire:model="tri" class="text-sm border-gray-300 rounded px-2 py-1">
                <option value="asc">⬆️ Plus anciens</option>
                <option value="desc">⬇️ Plus récents</option>
            </select>
        </div>
    </div>

    {{-- 🎛️ Filtres status --}}
    <div class="flex items-center gap-3 mb-4">
        @foreach(['valide' => '✅', 'en_attente' => '⏳', 'refuse' => '❌'] as $key => $icon)
            <button
                wire:click="$set('filtreStatut', '{{ $key }}')"
                class="text-sm px-3 py-1 rounded border transition
                    {{ $filtreStatus === $key
                        ? 'bg-blue-600 text-white border-blue-700'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ $icon }} {{ ucfirst(str_replace('_', ' ', $key)) }}
            </button>
        @endforeach

        @if($filtreStatus)
            <button wire:click="$set('filtreStatus', null)" class="text-sm text-gray-600 underline">
                Réinitialiser
            </button>
        @endif
    </div>

    {{-- 📅 Tableau des rendez-vous --}}
    <table class="w-full text-sm border">
        <thead class="bg-blue-50">
            <tr>
                <th class="p-2 border">Client</th>
                <th class="p-2 border">Date</th>
                <th class="p-2 border">Heure</th>
                <th class="p-2 border">Statut</th>
                <th class="p-2 border">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rendezVous as $rdv)
                @php
                    $rowClass = match($rdv->status) {
                        'valide' => 'bg-green-50',
                        'refuse' => 'bg-red-50',
                        'en_attente' => 'bg-yellow-50',
                        default => ''
                    };
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="border p-2">{{ $rdv->client->name }}</td>
                    <td class="border p-2">{{ $rdv->date }}</td>
                    <td class="border p-2">{{ $rdv->heure }}</td>
                    <td class="border p-2"><x-badge :status="$rdv->status" /></td>
                    <td class="border p-2">
                        @if($rdv->status === 'en_attente')
                            <button wire:click="mettreAJourStatus({{ $rdv->id }}, 'valide')"
                                class="px-2 py-1 bg-green-600 text-white rounded text-sm mr-2">✅</button>

                            <button
                                onclick="if (confirm('Es-tu sûr de vouloir refuser ce rendez-vous ?')) {
                                    Livewire.dispatch('refuser-rdv', { id: {{ $rdv->id }} });
                                }"
                                class="px-2 py-1 bg-red-600 text-white rounded text-sm">❌</button>
                        @else
                            <span class="text-gray-400 italic">Aucune action</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-gray-500 p-4 italic">
                        Aucun rendez-vous pour ce filtre.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 📄 Pagination --}}
    <div class="mt-4">
        {{ $rendezVous->links() }}
    </div>
</div>
