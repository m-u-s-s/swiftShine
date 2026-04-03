<div
    x-data="{ show: false, message: '', type: 'success' }"
    x-show="show"
    x-transition.opacity.scale.duration.300ms
    x-init="
        Livewire.on('toast', (msg, toastType = 'success') => {
            message = msg;
            type = toastType;
            show = true;

            try {
                new Audio(type === 'success' ? '/sounds/success.mp3' : '/sounds/error.mp3').play();
            } catch (e) {}

            setTimeout(() => show = false, 3500);
        });
    "
    class="fixed top-5 right-5 z-[9999]"
    style="display: none;"
>
    <div
        class="min-w-[260px] max-w-sm rounded-2xl shadow-xl border px-4 py-3 text-sm font-medium"
        :class="{
            'bg-green-50 text-green-700 border-green-200': type === 'success',
            'bg-red-50 text-red-700 border-red-200': type === 'error',
            'bg-amber-50 text-amber-700 border-amber-200': type === 'warning',
            'bg-blue-50 text-blue-700 border-blue-200': type === 'info',
        }"
    >
        <div class="flex items-start gap-3">
            <div class="text-lg">
                <span x-show="type === 'success'">✅</span>
                <span x-show="type === 'error'">❌</span>
                <span x-show="type === 'warning'">⚠️</span>
                <span x-show="type === 'info'">ℹ️</span>
            </div>

            <div class="flex-1" x-text="message"></div>
        </div>
    </div>
</div>