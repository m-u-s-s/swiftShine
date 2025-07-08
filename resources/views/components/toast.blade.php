<div
    x-data="{ show: false, message: '', type: 'success' }"
    x-show="show"
    x-transition
    x-init="
        Livewire.on('toast', (msg, toastType = 'success') => {
            message = msg;
            type = toastType;
            show = true;

            // 🔊 Son
            new Audio(type === 'success' ? '/sounds/success.mp3' : '/sounds/error.mp3').play();

            setTimeout(() => show = false, 4000);
        });
    "
    :class="{
        'bg-green-600 animate-pulse': type === 'success' && show,
        'bg-red-600 animate-pulse': type === 'error' && show
    }"
    class="fixed bottom-6 right-6 text-white px-5 py-3 rounded shadow-lg z-50 flex items-center space-x-3"
    style="display: none;"
>
    {{-- ✅ Icône --}}
    <template x-if="type === 'success'">
        <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
    </template>
    <template x-if="type === 'error'">
        <svg class="w-5 h-5 animate-ping" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </template>

    <span x-text="message" class="text-sm font-medium"></span>
</div>
