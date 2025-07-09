<div class="p-6 space-y-8">

    <x-admin.recapitulatif-acces />

    <h2 class="text-2xl font-bold text-blue-900">🛠️ Outils administrateur</h2>

    {{-- 📤 Export global --}}
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">📤 Exportation des données</h3>
        <livewire:admin.export-tools />
    </div>

    {{-- 📥 Import CSV --}}
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">📥 Importation CSV (Clients ou RDV)</h3>
        <livewire:admin.import-csv />
    </div>

    {{-- 📊 Statistiques dynamiques --}}
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">📊 Statistiques dynamiques</h3>
        <livewire:admin.stats-globale />
    </div>

    {{-- 🔐 Logs et notifications --}}
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">🔐 Logs système & notifications</h3>
        <livewire:admin.logs-activity />
    </div>

    {{-- 🧪 Outils de test --}}
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">🧪 Fonctions de test & seeders</h3>
        <livewire:admin.outils-de-test />
    </div>
</div>
