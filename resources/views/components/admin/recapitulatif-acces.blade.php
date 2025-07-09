<div x-data="{ open: false }" class="bg-white p-4 rounded shadow-md space-y-4">

    <div class="flex justify-between items-center">
        <h2 class="text-lg font-bold text-blue-900">📄 Récapitulatif des accès par rôle</h2>
        <button @click="open = !open"
                class="text-sm text-blue-600 hover:underline">
            <span x-show="!open">🔎 Voir</span>
            <span x-show="open">🔽 Masquer</span>
        </button>
    </div>

    <div x-show="open" x-transition>
        <table class="w-full text-sm table-auto border mt-3">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-2 text-left">Fonctionnalité</th>
                    <th class="px-2 py-2 text-center">👑 Admin</th>
                    <th class="px-2 py-2 text-center">👨‍🔧 Employé</th>
                    <th class="px-2 py-2 text-center">👤 Client</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach([
                    '🏠 Dashboard personnel' => [true, true, true],
                    '📅 Prendre rendez-vous (publique)' => [false, false, true],
                    '📋 Voir ses rendez-vous' => [false, true, true],
                    '✅ Valider un RDV (1 à 1)' => [false, true, false],
                    '✅ Valider plusieurs RDV' => [false, true, false],
                    '💬 Laisser un feedback' => [false, false, true],
                    '⭐ Modifier / supprimer feedback' => [false, false, true],
                    '🧾 Voir feedbacks reçus' => [true, true, true],
                    '📌 Notifications globales' => [true, true, true],
                    '📤 Export PDF / CSV' => [true, false, false],
                    '📥 Import CSV' => [true, false, false],
                    '📊 Statistiques dynamiques' => [true, false, false],
                    '🔐 Connexions Jetstream' => [true, true, true],
                    '📜 Logs d’activité' => [true, false, false],
                    '👥 Gestion des utilisateurs' => [true, false, false],
                    '⚙️ Modifier limites RDV' => [true, true, false],
                ] as $label => [$admin, $employe, $client])
                    <tr>
                        <td class="px-2 py-2">{{ $label }}</td>
                        <td class="px-2 py-2 text-center">{!! $admin ? '✅' : '—' !!}</td>
                        <td class="px-2 py-2 text-center">{!! $employe ? '✅' : '—' !!}</td>
                        <td class="px-2 py-2 text-center">{!! $client ? '✅' : '—' !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
