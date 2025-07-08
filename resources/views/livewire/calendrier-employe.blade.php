<div>
    <h2 class="text-xl font-bold mb-4">Mon planning</h2>

    <div id='calendar'></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                allDaySlot: false,
                slotDuration: '00:30:00',
                events: @json($events),
            });

            calendar.render();
        });
    </script>
</div>

