<div class="bg-white p-4 rounded shadow border">
    <h3 class="text-lg font-semibold text-blue-900 mb-4">💬 Feedbacks reçus de vos clients</h3>

    <div class="divide-y">
        @forelse($feedbacks as $feedback)
        <x-feedback-card :feedback="$feedback" />
        @if(auth()->user()->is_admin)
        <div class="mt-2">
            <label class="text-sm text-gray-600">Réponse admin :</label>
            <textarea wire:model.debounce.500ms="reponse.{{ $feedback->id }}"
                class="w-full border rounded px-2 py-1 text-sm mt-1"
                rows="2">{{ $feedback->reponse_admin }}</textarea>
        </div>
        @endif

        @empty
        <div class="text-center text-gray-500 italic py-4">Aucun feedback reçu pour le moment.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $feedbacks->links() }}</div>

</div>