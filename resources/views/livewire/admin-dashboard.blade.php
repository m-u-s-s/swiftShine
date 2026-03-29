
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

        <div class="bg-white p-4 rounded shadow border mt-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">📤 Exporter les feedbacks (PDF)</h3>

            <form action="{{ route('admin.feedbacks.export') }}" method="GET" target="_blank" class="space-y-3 md:flex md:items-end md:gap-4">
                {{-- 🔎 Filtre employé --}}
                <div class="flex flex-col">
                    <label for="export_employe_id" class="text-sm text-gray-600">Employé :</label>
                    <select name="employe_id" id="export_employe_id" class="border rounded px-2 py-1 text-sm">
                        <option value="">— Tous —</option>
                        @foreach($employes as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 🔎 Filtre client --}}
                <div class="flex flex-col">
                    <label for="client_id" class="text-sm text-gray-600">Client :</label>
                    <select name="client_id" id="client_id" class="border rounded px-2 py-1 text-sm">
                        <option value="">— Tous —</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 📤 Bouton export --}}
                <div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 transition">
                        📄 Télécharger le PDF
                    </button>
                </div>
            </form>
        </div>


        <h2 class="text-2xl font-bold text-blue-900">🛡️ Tableau de bord administrateur</h2>

        {{-- ✅ Toast global --}}
        <x-toast />

        <livewire:admin.feedback-stats />


        <div class="bg-white p-5 rounded shadow mt-8">
            <h2 class="text-lg font-semibold text-blue-900 mb-4">🧩 Limites journalières des employés</h2>

            {{-- 🔽 Sélecteur d’employé --}}
            <div class="mb-4">
                <label for="dashboard_employe_id" class="text-sm font-medium text-gray-700">Choisir un employé :</label>
                <select wire:model="employeSelectionne" id="dashboard_employe_id"
                    class="mt-1 block w-64 border-gray-300 rounded shadow-sm text-sm">
                    <option value="">-- Sélectionner --</option>
                    @foreach($employes as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($employeSelectionne)
            <div class="space-y-2">
                @foreach(\Carbon\Carbon::now()->startOfWeek()->daysUntil(\Carbon\Carbon::now()->endOfWeek()) as $jour)
                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                    <div class="text-sm text-gray-700 font-medium w-1/3">
                        {{ $jour->translatedFormat('l d F') }}
                    </div>
                    <div class="w-2/3">
                        @livewire('modifier-limite-jour', [
                        'date' => $jour->format('Y-m-d'),
                        'user_id' => $employeSelectionne,
                        'fromAdmin' => true
                        ], key($jour->format('Ymd') . '-' . $employeSelectionne))
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- 📊 Statistiques / Graphiques --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div id="chartStats" class="bg-white rounded shadow p-4"></div>
            <div id="chartMensuel" class="bg-white rounded shadow p-4"></div>
        </div>

        {{-- 📅 Calendrier interactif global --}}
        <div class="bg-white rounded shadow mt-6 p-4">
            <h3 class="text-lg font-semibold mb-2">📆 Calendrier global</h3>
            <div id="fullcalendar-admin"></div>
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

        <livewire:admin-feedbacks />
        <livewire:admin.gestion-utilisateurs />
        <livewire:admin.agenda-hebdomadaire />
        <livewire:notifications />
        <x-admin.recapitulatif-acces />


    </div>

<!-- script calendrier et apexcharts -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        let chartInstance = null;
        let chartMensuelInstance = null;
        let adminCalendarInstance = null;
        let livewireListenersRegistered = false;

        function initAdminCharts() {
            const chartStatsEl = document.querySelector('#chartStats');
            const chartMensuelEl = document.querySelector('#chartMensuel');

            if (!chartStatsEl || !chartMensuelEl) return;

            if (chartInstance) {
                chartInstance.destroy();
            }

            if (chartMensuelInstance) {
                chartMensuelInstance.destroy();
            }

            chartInstance = new ApexCharts(chartStatsEl, {
                chart: {
                    type: 'donut',
                    height: 300
                },
                series: [0, 0, 0],
                labels: ['Confirmés', 'En attente', 'Refusés'],
                colors: ['#16a34a', '#eab308', '#dc2626']
            });

            chartMensuelInstance = new ApexCharts(chartMensuelEl, {
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

            chartInstance.render();
            chartMensuelInstance.render();
        }

        function initAdminCalendar() {
            const calendarEl = document.getElementById('fullcalendar-admin');
            if (!calendarEl) return;

            if (adminCalendarInstance) {
                adminCalendarInstance.destroy();
            }

            adminCalendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                events: @js($rdvs),
                eventClick(info) {
                    alert('RDV avec ' + info.event.title);
                }
            });

            adminCalendarInstance.render();
        }

        function registerAdminDashboardListeners() {
            if (livewireListenersRegistered) return;
            livewireListenersRegistered = true;

            Livewire.on('updateChartData', (event) => {
                const data = event?.data ?? event;

                if (!chartInstance) return;

                chartInstance.updateSeries([
                    data.confirme || 0,
                    data.attente || 0,
                    data.refuse || 0
                ]);
            });

            Livewire.on('updateMonthlyChart', (event) => {
                const data = event?.data ?? event;

                if (!chartMensuelInstance) return;

                chartMensuelInstance.updateSeries([{
                    name: 'RDV',
                    data: data
                }]);
            });
        }

        function bootAdminDashboard() {
            initAdminCharts();
            initAdminCalendar();
            registerAdminDashboardListeners();
        }

        document.addEventListener('livewire:load', bootAdminDashboard);
        document.addEventListener('livewire:navigated', bootAdminDashboard);
    </script>
    @endpush
