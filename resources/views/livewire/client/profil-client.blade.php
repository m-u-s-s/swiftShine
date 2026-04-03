<div class="p-4 md:p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-blue-900">👤 Mon profil</h2>
            <p class="text-sm text-gray-500">
                Retrouvez vos informations, vos habitudes de réservation et vos raccourcis utiles.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('client.rendezvous.create') }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                ➕ Nouveau rendez-vous
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl shadow border">
            <p class="text-sm text-gray-500">Total missions</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border">
            <p class="text-sm text-gray-500">À venir</p>
            <p class="text-2xl font-bold text-blue-700">{{ $stats['avenir'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border">
            <p class="text-sm text-gray-500">Terminées</p>
            <p class="text-2xl font-bold text-emerald-700">{{ $stats['termine'] }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow border">
            <p class="text-sm text-gray-500">Urgentes</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['urgentes'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow border p-5">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Informations du compte</h3>

            <div class="space-y-3 text-sm text-gray-700">
                <div>
                    <p class="text-gray-500">Nom</p>
                    <p class="font-medium text-slate-900">{{ $client->name }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Email</p>
                    <p class="font-medium text-slate-900">{{ $client->email }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Rôle</p>
                    <p class="font-medium text-slate-900 capitalize">{{ $client->role }}</p>
                </div>

                <div class="pt-2 flex flex-wrap gap-3">
                    <a href="{{ route('profile.show') }}" class="text-blue-600 underline">
                        Gérer mon compte et ma sécurité
                    </a>

                    <a href="{{ route('client.rendezvous.create') }}" class="text-blue-600 underline">
                        Nouveau rendez-vous
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow border p-5">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Dernières préférences</h3>

            @if($dernierePreference)
            <div class="space-y-2 text-sm text-gray-700">
                <p><span class="font-medium">Service :</span> {{ ucfirst(str_replace('_', ' ', $dernierePreference->service_type ?? '—')) }}</p>
                <p><span class="font-medium">Type de lieu :</span> {{ ucfirst($dernierePreference->type_lieu ?? '—') }}</p>
                <p><span class="font-medium">Fréquence :</span> {{ ucfirst(str_replace('_', ' ', $dernierePreference->frequence ?? '—')) }}</p>
                <p><span class="font-medium">Priorité :</span> {{ ucfirst($dernierePreference->priorite ?? '—') }}</p>
                <p><span class="font-medium">Créneau favori :</span> {{ $dernierePreference->is_favorite_slot ? 'Oui' : 'Non' }}</p>
            </div>
            @else
            <p class="text-sm text-gray-500 italic">Aucune préférence enregistrée pour le moment.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow border p-5">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Adresses récentes</h3>

        <div class="space-y-3">
            @forelse($adressesRecentes as $adresse)
            <div class="border rounded-xl p-3 bg-gray-50">
                <p class="font-medium text-gray-800">{{ $adresse->adresse }}</p>
                <p class="text-sm text-gray-600">{{ $adresse->ville ?? '—' }} {{ $adresse->code_postal ?? '' }}</p>
            </div>
            @empty
            <div class="text-sm text-gray-500 italic">
                Aucune adresse récente.
            </div>
            @endforelse
        </div>
    </div>
</div>