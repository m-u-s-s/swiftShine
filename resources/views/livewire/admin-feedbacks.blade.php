<div class="bg-white p-4 rounded shadow border space-y-4">

    <h3 class="text-lg font-semibold text-blue-900">💬 Feedbacks clients</h3>

    {{-- 🔎 Filtres --}}
    <div class="md:flex gap-4 items-end space-y-2 md:space-y-0">
        <div class="flex flex-col">
            <label class="text-sm text-gray-600">Employé</label>
            <select wire:model="employe_id" class="border rounded px-2 py-1 text-sm">
                <option value="">— Tous —</option>
                @foreach($employes as $e)
                <option value="{{ $e->id }}">{{ $e->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col">
            <label class="text-sm text-gray-600">Client</label>
            <select wire:model="client_id" class="border rounded px-2 py-1 text-sm">
                <option value="">— Tous —</option>
                @foreach($clients as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- 📤 Boutons export --}}
        <div class="flex gap-2 mt-4 md:mt-0">
            <button wire:click="exportPdf" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                📄 Export PDF
            </button>
            <button wire:click="exportCsv" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                📥 Export CSV
            </button>
        </div>
    </div>

    {{-- 📋 Liste des feedbacks --}}
    <div class="divide-y">
        @forelse($feedbacks as $feedback)
        <x-feedback-card :feedback="$feedback" />
        <div class="mt-2">
            <label class="text-sm text-gray-600">Réponse admin :</label>
            <textarea wire:model.debounce.500ms="reponse.{{ $feedback->id }}"
                class="w-full border rounded px-2 py-1 text-sm mt-1"
                rows="2">{{ $feedback->reponse_admin }}</textarea>
        </div>
        @empty
        <div class="text-center text-gray-500 italic py-4">Aucun feedback trouvé.</div>
        @endforelse
    </div>



    {{-- 📄 Pagination --}}
    <div class="mt-4">{{ $feedbacks->links() }}</div>
</div>