@props([
    'title' => 'Aucun résultat',
    'message' => 'Aucune donnée disponible pour le moment.',
])

<div class="bg-white border rounded-2xl p-8 text-center">
    <div class="mx-auto w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-2xl">
        ✨
    </div>

    <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">{{ $message }}</p>
</div>