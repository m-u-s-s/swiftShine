<x-app-layout>
    <div class="p-6 space-y-6">
        <h2 class="text-2xl font-bold mb-4">👷 Mon tableau de bord employé</h2>

        {{-- 🗓️ Gestion des limites de rendez-vous --}}
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">🧭 Limites journalières</h3>

            <table class="w-full text-sm border border-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        @foreach(['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'] as $i => $label)
                            <th class="px-3 py-2 border text-center capitalize">
                                {{ ucfirst($label) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach(range(0, 6) as $i)
                            @php
                                $date = now()->startOfWeek()->addDays($i)->toDateString();
                            @endphp
                            <td class="p-2 border text-center align-top">
                                @livewire('modifier-limite-jour', [
                                    'date' => $date,
                                    'user_id' => auth()->id()
                                ], key('limite-emp-' . $date))
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Autres composants employés --}}
        @livewire('disponibilites-manager')
        @livewire('mes-rendez-vous')
        @livewire('employe.calendrier-employe')
    </div>
</x-app-layout>
