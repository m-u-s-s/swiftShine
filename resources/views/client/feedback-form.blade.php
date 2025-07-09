@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow mt-6">

    <h2 class="text-2xl font-bold text-blue-900 mb-4">🗣️ Laisser un feedback</h2>

    <p class="text-sm text-gray-600 mb-4">
        📅 <strong>Rendez-vous du {{ $rendezVous->date }} à {{ $rendezVous->heure }}</strong><br>
        🧑‍💼 Avec : <strong>{{ $rendezVous->employe->name ?? '—' }}</strong>
    </p>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('client.feedback.store', $rendezVous->id) }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Note</label>
            <x-star-rating model="note" />
            <input type="hidden" name="note" id="note-hidden" value="3">
        </div>

        <div>
            <label for="commentaire" class="block text-sm font-medium text-gray-700">Commentaire</label>
            <textarea name="commentaire" rows="4" required
                      class="w-full border rounded px-3 py-2 text-sm focus:ring focus:ring-blue-200">{{ old('commentaire') }}</textarea>
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                💾 Envoyer mon feedback
            </button>
        </div>
    </form>
</div>

{{-- Script Livewire-Alpine pour transférer la note --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.effect(() => {
            let rating = Alpine.store('rating')?.rating;
            if (rating !== undefined) {
                document.getElementById('note-hidden').value = rating;
            }
        });
    });
</script>
@endsection
