<div>
    <div class="p-4">
        <h2 class="text-xl font-bold mb-4">Gérer mes disponibilités</h2>

        @if (session()->has('message'))
        <div class="text-green-600 mb-2">{{ session('message') }}</div>
        @endif

        <form wire:submit.prevent="ajouter" class="space-y-2">
            <input type="date" wire:model="date" class="border p-2 rounded w-full">
            <input type="time" wire:model="heure_debut" class="border p-2 rounded w-full">
            <input type="time" wire:model="heure_fin" class="border p-2 rounded w-full">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Ajouter</button>
        </form>

        <hr class="my-4">

        <h3 class="text-lg font-semibold">Mes créneaux :</h3>
        <ul class="space-y-1">
            @foreach ($disponibilites as $dispo)
            <li class="border p-2 rounded flex justify-between items-center">
                <div>
                    {{ $dispo->date }} : {{ $dispo->heure_debut }} - {{ $dispo->heure_fin }}
                </div>
                <button wire:click="supprimer({{ $dispo->id }})" class="text-red-600">Supprimer</button>
            </li>
            @endforeach
        </ul>
    </div>

</div>