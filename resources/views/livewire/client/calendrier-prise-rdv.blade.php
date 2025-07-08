<div class="space-y-4">
    {{-- 🎛️ Filtres / options --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <label class="text-sm font-medium text-gray-700">Filtrer par jour :</label>
            <select wire:model="filtreJour" class="text-sm border-gray-300 rounded px-2 py-1">
                <option value="">Tous</option>
                <option value="Monday">Lundi</option>
                <option value="Tuesday">Mardi</option>
                <option value="Wednesday">Mercredi</option>
                <option value="Thursday">Jeudi</option>
                <option value="Friday">Vendredi</option>
                <option value="Saturday">Samedi</option>
                <option value="Sunday">Dimanche</option>
            </select>
        </div>
        <button wire:click="$refresh" class="text-sm px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">🔄 Rafraîchir</button>
    </div>


    <div class="flex items-center justify-between mb-4">
        <button wire:click="semainePrecedente" class="text-sm px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded">⬅️ Semaine précédente</button>
        <div class="text-gray-600 text-sm">
            Semaine du {{ \Carbon\Carbon::now()->startOfWeek()->addWeeks($semaineOffset)->translatedFormat('d M') }}
            au {{ \Carbon\Carbon::now()->endOfWeek()->addWeeks($semaineOffset)->translatedFormat('d M Y') }}
        </div>
        <button wire:click="semaineSuivante" class="text-sm px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded">Semaine suivante ➡️</button>
    </div>

    {{-- 📅 Affichage semaine (lun → dim) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($disponibilites as $jour => $heures)
        <div class="p-4 border rounded shadow bg-white">
            <h4 class="text-sm font-semibold text-blue-800 mb-2">
                {{ \Carbon\Carbon::parse($jour)->translatedFormat('l d F Y') }}
            </h4>

            <div class="flex flex-wrap gap-2">
                @forelse($heures as $heure)
                <button
                    wire:click="choisir('{{ $jour }}', '{{ $heure }}')"
                    class="px-3 py-1 text-sm rounded border transition-all duration-150 ease-in-out
                            {{ $selectedDate === $jour && $selectedHeure === $heure
                                ? 'bg-blue-600 text-white font-semibold'
                                : 'bg-gray-100 hover:bg-blue-100' }}">
                    🕒 {{ $heure }}
                </button>
                @empty
                <span class="text-gray-500 text-sm animate-pulse">Aucun créneau ce jour</span>
                @endforelse
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-500 italic animate-pulse">
            Aucun créneau disponible pour la semaine actuelle.
        </div>
        @endforelse
    </div>

    @if($confirmation)
    <div
        x-data=\"{ show: true }\"
        x-init=\"setTimeout(()=> show = false, 4000)\"
        x-show=\"show\"
        x-transition
        class=\"bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mt-6 text-sm animate-pulse\">
        ✅ Vous avez sélectionné le {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l d F') }} à {{ $selectedHeure }}.
    </div>
    @endif

</div>