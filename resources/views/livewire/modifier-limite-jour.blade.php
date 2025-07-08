<div class="flex items-center justify-between gap-4 p-2 border rounded bg-white shadow-sm">
    <div class="flex flex-col text-sm">
        <span class="text-gray-600 font-medium">{{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}</span>
        @if($record = \App\Models\LimiteJournaliere::where('user_id', $user_id)->where('date', $date)->first())
            @if($record->verrou_admin)
                <span class="text-red-600 text-xs font-semibold">🔒 Verrouillé par admin</span>
            @endif
        @endif
    </div>

    <div class="flex items-center gap-2">
        <input type="number" min="0" wire:model.lazy="limite"
            class="w-16 text-sm border-gray-300 rounded px-2 py-1 text-center
            @if($record && $record->verrou_admin && !$fromAdmin) bg-gray-100 cursor-not-allowed @endif"
            @if($record && $record->verrou_admin && !$fromAdmin) disabled @endif
        >
        <span class="text-xs text-gray-500">RDV max</span>
    </div>
</div>
