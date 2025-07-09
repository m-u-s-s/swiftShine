@props(['feedback'])

@php
    $rdv = $feedback->rendezVous;
    $status = $rdv->statut ?? 'en attente';

    $statusColor = match($status) {
        'validé' => 'bg-green-100 text-green-700',
        'refusé' => 'bg-red-100 text-red-700',
        default => 'bg-yellow-100 text-yellow-700',
    };
@endphp

<div class="border-b py-3 space-y-1">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
            👤 <strong>{{ $feedback->client->name }}</strong>
            | 🧑‍💼 {{ $rdv->employe->name ?? '—' }}
            | 📅 {{ $feedback->created_at->translatedFormat('d M Y') }}
        </div>

        <span class="text-xs px-2 py-1 rounded {{ $statusColor }}">
            🏷️ {{ ucfirst($status) }}
        </span>
    </div>

    <div class="text-sm text-gray-800">
        {{ $feedback->commentaire }}
    </div>

    <div class="flex items-center gap-1">
        <x-star-rating :rating="$feedback->note" readonly />
        <span class="text-xs text-gray-500">({{ $feedback->note }}/5)</span>
    </div>

    @if(Route::has('admin.rendezvous.show'))
        <div class="mt-1">
            <a href="{{ route('admin.rendezvous.show', $rdv->id) }}"
               class="text-xs text-blue-600 underline hover:text-blue-800">
                🔎 Voir rendez-vous
            </a>
        </div>
    @endif
</div>
