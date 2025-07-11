@props(['status', 'clickable' => false])

@php
    $classes = match(strtolower($status)) {
        'valide' => 'bg-green-100 text-green-700',
        'refuse' => 'bg-red-100 text-red-700',
        'en_attente', 'attente' => 'bg-yellow-100 text-yellow-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span @if($clickable) wire:click="$emit('filterByStatus', '{{ $status }}')" @endif
      class="text-xs px-2 py-1 rounded cursor-pointer {{ $classes }}
             @if($clickable) hover:opacity-80 transition @endif">
    🏷️ {{ ucfirst($status) }}
</span>
