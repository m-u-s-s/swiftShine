<div class="bg-white p-4 rounded shadow border">
    <h3 class="text-lg font-semibold text-blue-900 mb-4">💬 Mes feedbacks envoyés</h3>

    <div class="flex flex-wrap gap-4 items-end mb-4">
        <div>
            <label class="text-sm text-gray-600">Note min :</label>
            <select wire:model="noteMin" class="border rounded px-2 py-1 text-sm">
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ $i }} ★ et +</option>
                    @endfor
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-600">Trier :</label>
            <select wire:model="sort" class="border rounded px-2 py-1 text-sm">
                <option value="desc">🔽 Plus récents</option>
                <option value="asc">🔼 Plus anciens</option>
            </select>
        </div>
    </div>

    @forelse($feedbacks as $feedback)
    <div class="border-b py-3 space-y-1">

        {{-- Si on édite ce feedback --}}
        @if($editingId === $feedback->id)
        <div class="text-sm text-gray-700">
            🧑‍💼 {{ $feedback->rendezVous->employe->name ?? '—' }} | 📅 {{ $feedback->created_at->translatedFormat('d M Y') }}
        </div>

        <textarea wire:model.defer="commentaire"
            class="w-full border rounded px-2 py-1 text-sm"
            rows="3"></textarea>

        <div class="flex items-center gap-2 mt-1">
            <label class="text-sm">Note :</label>
            <div x-data="{ note: @entangle('note'), hover: null }" class="flex gap-1 mt-2">
                @for ($i = 1; $i <= 5; $i++)
                    <button type="button"
                    x-on:click="note = {{ $i }}"
                    x-on:mouseover="hover = {{ $i }}"
                    x-on:mouseleave="hover = null"
                    x-on:keydown.enter.prevent="note = {{ $i }}"
                    :aria-label="'Note ' + {{ $i }}"
                    class="text-2xl transition-transform transform focus:outline-none focus:ring-2 focus:ring-blue-400 rounded-sm"
                    :class="(hover ?? note) >= {{ $i }} ? 'text-yellow-400 scale-110' : 'text-gray-300'"
                    tabindex="0">
                    ★
                    </button>
                    @endfor
            </div>


        </div>

        <div class="mt-2 flex gap-2">
            <button wire:click="save" class="bg-green-600 text-white text-sm px-3 py-1 rounded">💾 Sauver</button>
            <button wire:click="cancelEdit" class="text-gray-500 text-sm underline">Annuler</button>
        </div>

        @else
        <div class="text-sm text-gray-700">
            🧑‍💼 {{ $feedback->rendezVous->employe->name ?? '—' }} | 📅 {{ $feedback->created_at->translatedFormat('d M Y') }}
        </div>

        <div class="text-sm">{{ $feedback->commentaire }}</div>

        <div class="text-sm text-yellow-600">
            {{ str_repeat('★', $feedback->note) }}{{ str_repeat('☆', 5 - $feedback->note) }}
            <span class="text-xs text-gray-400 ml-1">({{ $feedback->note }}/5)</span>
        </div>

        <div class="flex gap-2 mt-1 text-sm">
            <button wire:click="edit({{ $feedback->id }})" class="text-blue-600 underline">✏️ Modifier</button>
            <button onclick="if(confirm('Supprimer ce feedback ?')) @this.delete({{ $feedback->id }})"
                class="text-red-600 underline">🗑️ Supprimer</button>
        </div>
        @endif
    </div>
    @empty
    <div class="text-center text-gray-500 italic">Aucun feedback encore envoyé.</div>
    @endforelse


    <div class="mt-4">{{ $feedbacks->links() }}</div>
    <x-star-rating wire:model="note" />

</div>