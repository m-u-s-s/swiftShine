<x-app-layout>
    <div class="p-6 space-y-6">
        <h2 class="text-2xl font-bold text-blue-900">🛡️ Tableau de bord administrateur</h2>

        {{-- ✅ Toast global --}}
        <x-toast />

        {{-- 📊 Statistiques / Graphiques --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div id="chartStats" class="bg-white rounded shadow p-4"></div>
            <div id="chartMensuel" class="bg-white rounded shadow p-4"></div>
        </div>

        {{-- 📅 Calendrier interactif global --}}
        <div class="bg-white rounded shadow mt-6 p-4">
            <h3 class="text-lg font-semibold mb-2">📆 Calendrier global</h3>
            <div id="adminCalendar"></div>
        </div>

        {{-- ⚙️ Configuration des limites de RDV pour chaque employé --}}
        <div class="bg-white p-4 rounded shadow border mt-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">🛠️ Gestion des limites journalières</h3>

            @foreach($employes as $employe)
            <div class="mb-6 border-t pt-4">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">👤 {{ $employe->name }}</h4>

                <div class="space-y-2">
                    @foreach(\Carbon\Carbon::now()->startOfWeek()->daysUntil(\Carbon\Carbon::now()->endOfWeek()) as $jour)
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                        <div class="text-sm text-gray-700 font-medium w-1/3">
                            {{ $jour->translatedFormat('l d F') }}
                        </div>
                        <div class="w-2/3">
                            @livewire('modifier-limite-jour', [
                            'date' => $jour->format('Y-m-d'),
                            'user_id' => $employe->id,
                            'fromAdmin' => true
                            ], key($jour->format('Ymd') . '-' . $employe->id))
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>


    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let chartInstance, chartMensuelInstance;

        document.addEventListener('livewire:load', () => {
            chartInstance = new ApexCharts(document.querySelector("#chartStats"), {
                chart: {
                    type: 'donut',
                    height: 300
                },
                series: [0, 0, 0],
                labels: ['Validés', 'En attente', 'Refusés'],
                colors: ['#16a34a', '#eab308', '#dc2626']
            });
            chartInstance.render();

            chartMensuelInstance = new ApexCharts(document.querySelector("#chartMensuel"), {
                chart: {
                    type: 'line',
                    height: 300
                },
                series: [{
                    name: 'RDV',
                    data: Array(12).fill(0)
                }],
                xaxis: {
                    categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
                },
                colors: ['#3b82f6']
            });
            chartMensuelInstance.render();

            Livewire.on('updateChartData', (data) => {
                chartInstance.updateSeries([
                    data.valide || 0,
                    data.attente || 0,
                    data.refuse || 0
                ]);
            });

            Livewire.on('updateMonthlyChart', (data) => {
                chartMensuelInstance.updateSeries([{
                    name: 'RDV',
                    data: data
                }]);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('fullcalendar-admin');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                events: @js($rdvs),
                eventClick(info) {
                    alert('RDV avec ' + info.event.title);
                }
            });
            calendar.render();
        });
    </script>
    @endpush
</x-app-layout>