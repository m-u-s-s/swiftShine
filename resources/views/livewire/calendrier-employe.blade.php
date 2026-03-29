<div>
    <h2 class="text-xl font-bold mb-4">Mon planning</h2>
    <div wire:ignore id="calendar"></div>
</div>

@push('scripts')
<script>
    let employeCalendarInstance = null;

    function initEmployeCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        if (employeCalendarInstance) {
            employeCalendarInstance.destroy();
        }

        employeCalendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            locale: 'fr',
            allDaySlot: false,
            slotDuration: '00:30:00',
            events: @json($events),
        });

        employeCalendarInstance.render();
    }

    document.addEventListener('livewire:load', initEmployeCalendar);
    document.addEventListener('livewire:navigated', initEmployeCalendar);
</script>
@endpush