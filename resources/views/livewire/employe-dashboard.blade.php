<div class="p-6 space-y-6">

    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
    <div class="bg-white p-4 rounded shadow space-y-2 mt-4">
        <h3 class="text-sm font-semibold text-blue-800">🔐 Connexions actives</h3>

        @foreach ($sessions = Auth::user()->sessions ?? [] as $session)
        <div class="flex items-center justify-between text-sm border-b py-2">
            <div>
                {{ $session->agent['platform'] ?? 'Inconnu' }} -
                {{ $session->agent['browser'] ?? 'Navigateur inconnu' }}
                <br>
                <span class="text-xs text-gray-500">
                    {{ $session->ip_address }},
                    dernière activité : {{ \Carbon\Carbon::parse($session->last_active)->diffForHumans() }}
                </span>
            </div>
            @if ($session->is_current_device)
            <span class="text-green-600 text-xs font-semibold">Appareil actuel</span>
            @endif
        </div>
        @endforeach
    </div>
    @endif
    <h2 class="text-2xl font-bold text-blue-900">👤 Tableau de bord employé</h2>

    {{-- ✅ Toast Livewire --}}
    <x-toast />

    {{-- 📋 Liste des rendez-vous avec filtres, tri, recherche --}}
    <div class="bg-white rounded shadow-md p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">📅 Mes rendez-vous</h3>
        <livewire:mes-rendez-vous />
    </div>

    {{-- ⚙️ Gestion des limites journalières --}}
    <div class="bg-white p-4 rounded shadow border mt-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">🛠️ Mes limites de RDV par jour</h3>

        <div class="space-y-2">
            @foreach(\Carbon\Carbon::now()->startOfWeek()->daysUntil(\Carbon\Carbon::now()->endOfWeek()) as $jour)
            <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                <div class="text-sm text-gray-700 font-medium w-1/3">
                    {{ $jour->translatedFormat('l d F') }}
                </div>
                <div class="w-2/3">
                    @livewire('modifier-limite-jour', [
                    'date' => $jour->format('Y-m-d'),
                    'user_id' => auth()->id(),
                    'fromAdmin' => false
                    ], key($jour->format('Ymd') . '-' . auth()->id()))
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<livewire:feedbacks-employe />
<livewire:employe.feedback-stats />
<livewire:employe.validation-multiple-rdv />
