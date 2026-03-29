<div class="bg-white p-4 rounded shadow border">
    <h3 class="text-lg font-semibold text-blue-900 mb-4">💬 Feedbacks reçus de vos clients</h3>

    <div class="divide-y">
        @forelse($feedbacks as $feedback)
            <x-feedback-card :feedback="$feedback" />
        @empty
            <div class="text-center text-gray-500 italic py-4">
                Aucun feedback reçu pour le moment.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $feedbacks->links() }}</div>
</div>