document.addEventListener('DOMContentLoaded', function () {
    const scheduledInput = document.getElementById('event_scheduled_at');
    const expiredInput = document.getElementById('event_expired_at');
    const form = document.querySelector('form');

    function pad(num) {
        return num.toString().padStart(2, '0');
    }

    function getMinExpiredDateString(scheduledDate) {
        const minExpiredDate = new Date(scheduledDate.getTime() + 60000);
        const year = minExpiredDate.getFullYear();
        const month = pad(minExpiredDate.getMonth() + 1);
        const day = pad(minExpiredDate.getDate());
        const hours = pad(minExpiredDate.getHours());
        const minutes = pad(minExpiredDate.getMinutes());
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    function updateExpiredMin() {
        if (scheduledInput.value) {
            const scheduledDate = new Date(scheduledInput.value);
            const minString = getMinExpiredDateString(scheduledDate);
            expiredInput.min = minString;

            if (expiredInput.value && expiredInput.value < minString) {
                expiredInput.value = '';
            }
        }
    }

    scheduledInput.addEventListener('change', updateExpiredMin);

    // Validação no envio só para garantir (opcional)
    form.addEventListener('submit', function (event) {
        if (scheduledInput.value && expiredInput.value) {
            const scheduledDate = new Date(scheduledInput.value);
            const minExpiredDate = new Date(scheduledDate.getTime() + 60000);
            const expiredDate = new Date(expiredInput.value);

            if (expiredDate < minExpiredDate) {
                alert('O horário de encerramento deve ser pelo menos 1 minuto após o agendamento.');
                event.preventDefault();
            }
        }
    });

    // Inicializa o min de expired ao carregar a página
    updateExpiredMin();
});
