<div class="space-y-4">

    @if (session('error'))
        <div class="text-sm text-red-600">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="text-sm text-gray-600">Type de données :</label>
            <select wire:model="type" class="border px-2 py-1 rounded text-sm">
                <option value="rendez_vous">📅 Rendez-vous</option>
                <option value="utilisateurs">👥 Utilisateurs</option>
                <option value="feedbacks">💬 Feedbacks</option>
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-600">Format :</label>
            <select wire:model="format" class="border px-2 py-1 rounded text-sm">
                <option value="csv">📥 CSV</option>
                <option value="pdf">📄 PDF</option>
            </select>
        </div>

        <button wire:click="export"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            📤 Exporter
        </button>
    </div>
</div>
