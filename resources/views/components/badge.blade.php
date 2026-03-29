@props(['status'])

@php
    $color = match($status) {
        'validé' => 'bg-[#2F9E44] text-white',
        'refusé' => 'bg-[#E03A2F] text-white',
        'en attente' => 'bg-[#003366] text-white',
        default => 'bg-gray-200 text-gray-700',
    };
@endphp

<span class="px-2 py-1 text-xs rounded {{ $color }}">
    {{ ucfirst($status) }}
</span>

