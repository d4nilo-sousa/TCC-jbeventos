document.addEventListener('DOMContentLoaded', function () {
    const scheduledInput = document.getElementById('event_scheduled_at');
    const expiredInput = document.getElementById('event_expired_at');

    const startInput = document.querySelector('input[name="start_date"]');
    const endInput = document.querySelector('input[name="end_date"]');

    const form = document.querySelector('form');

    function pad(num) {
        return num.toString().padStart(2, '0');
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = pad(date.getMonth() + 1);
        const day = pad(date.getDate());
        return `${year}-${month}-${day}`;
    }

    // ----------------------------
    // EVENTO - Exclusão Automática
    // ----------------------------
    function getMinExpiredDateString(scheduledDate) {
        const minExpiredDate = new Date(scheduledDate.getTime() + 60000); // +1 minuto
        const year = minExpiredDate.getFullYear();
        const month = pad(minExpiredDate.getMonth() + 1);
        const day = pad(minExpiredDate.getDate());
        const hours = pad(minExpiredDate.getHours());
        const minutes = pad(minExpiredDate.getMinutes());
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    function updateExpiredMin() {
        if (scheduledInput && scheduledInput.value && expiredInput) {
            const scheduledDate = new Date(scheduledInput.value);
            const minString = getMinExpiredDateString(scheduledDate);
            expiredInput.min = minString;

            if (expiredInput.value && new Date(expiredInput.value) < new Date(minString)) {
                expiredInput.value = '';
            }
        }
    }

    if (scheduledInput) {
        scheduledInput.addEventListener('change', updateExpiredMin);
        updateExpiredMin();
    }

    if (expiredInput && scheduledInput) {
        expiredInput.addEventListener('input', function () {
            if (!scheduledInput.value) return;
            const scheduledDate = new Date(scheduledInput.value);
            const minExpiredDate = new Date(scheduledDate.getTime() + 60000);
            const inputDate = new Date(expiredInput.value);

            if (expiredInput.value && inputDate < minExpiredDate) {
                expiredInput.value = '';
            }
        });
    }

    // ----------------------------
    // INTERVALO DE DATAS (Sem alert ou bloqueio)
    // ----------------------------
    function updateEndMin() {
        if (startInput && startInput.value && endInput) {
            const startDate = new Date(startInput.value);
            const minEndDate = new Date(startDate);
            minEndDate.setDate(startDate.getDate() + 1); // +1 dia
            endInput.min = formatDate(minEndDate);

            if (endInput.value && new Date(endInput.value) < minEndDate) {
                endInput.value = '';
            }
        } else if (endInput) {
            endInput.min = '';
        }
    }

    if (startInput) {
        startInput.addEventListener('change', updateEndMin);
        updateEndMin();
    }

    if (endInput && startInput) {
        endInput.addEventListener('input', function () {
            if (!startInput.value) return;
            const startDate = new Date(startInput.value);
            const minEndDate = new Date(startDate);
            minEndDate.setDate(startDate.getDate() + 1);

            if (endInput.value && new Date(endInput.value) < minEndDate) {
                endInput.value = '';
            }
        });
    }

    // ----------------------------
    // VALIDAÇÃO NO SUBMIT (Apenas exclusão automática)
    // ----------------------------
    form.addEventListener('submit', function (e) {
        if (scheduledInput && expiredInput && scheduledInput.value && expiredInput.value) {
            const scheduledDate = new Date(scheduledInput.value);
            const minExpiredDate = new Date(scheduledDate.getTime() + 60000);
            const expiredDate = new Date(expiredInput.value);

            if (expiredDate < minExpiredDate) {
                e.preventDefault();
                expiredInput.value = '';
                return;
            }
        }
    });
});
