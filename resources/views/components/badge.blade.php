@props(['status'])

@php
    $color = match($status) {
        'valide' => 'bg-green-100 text-green-700 border-green-300',
        'refuse' => 'bg-red-100 text-red-700 border-red-300',
        'en_attente' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
        default => 'bg-gray-100 text-gray-600 border-gray-300',
    };

    $label = match($status) {
        'valide' => '✅ Confirmé',
        'refuse' => '❌ Refusé',
        'en_attente' => '⏳ En attente',
        default => ucfirst($status),
    };
@endphp

<span class="text-xs font-medium px-2 py-1 border rounded {{ $color }}">
    {{ $label }}
</span>
