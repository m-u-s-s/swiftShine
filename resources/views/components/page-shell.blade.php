@props([
    'title',
    'subtitle' => null,
    'actions' => null,
])

<div class="p-4 md:p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-blue-900">{{ $title }}</h2>

            @if($subtitle)
                <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        @if($actions)
            <div class="flex flex-wrap gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>

    {{ $slot }}
</div>