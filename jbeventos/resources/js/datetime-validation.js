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

    /**
     * Atualiza o atributo 'min' do campo event_expired_at (Exclusão Automática)
     * para ser sempre 1 minuto após o event_scheduled_at.
     */
    function updateExpiredMin() {
        if (scheduledInput && scheduledInput.value && expiredInput) {
            const scheduledDate = new Date(scheduledInput.value);
            const minString = getMinExpiredDateString(scheduledDate);
            
            // Define o mínimo que o navegador irá respeitar
            expiredInput.min = minString; 

            // Limpa o valor se a data agendada for alterada para um momento posterior
            if (expiredInput.value && new Date(expiredInput.value) < new Date(minString)) {
                expiredInput.value = '';
            }
        }
    }

    if (scheduledInput) {
        scheduledInput.addEventListener('change', updateExpiredMin);
        // Garante que o mínimo seja configurado na carga inicial
        updateExpiredMin(); 
    }

    // ----------------------------
    // PREVINE DIGITAÇÃO INVÁLIDA NO EVENT_EXPIRED_AT (Feedback Imediato)
    // ----------------------------
    if (expiredInput && scheduledInput) {
        expiredInput.addEventListener('input', function () {
            if (!scheduledInput.value) return;

            const scheduledDate = new Date(scheduledInput.value);
            const minExpiredDate = new Date(scheduledDate.getTime() + 60000);

            const inputDate = new Date(expiredInput.value);
            
            // Se o valor inserido for inválido, limpa o campo
            if (expiredInput.value && inputDate < minExpiredDate) {
                 expiredInput.value = ''; 
                 // Opcional: Adicionar feedback visual aqui, como 'border-red-500'
            }
        });
    }

    // ----------------------------
    // INTERVALO DE DATAS
    // ----------------------------
    function updateEndMin() {
        if (startInput && startInput.value && endInput) {
            const startDate = new Date(startInput.value);
            const minEndDate = new Date(startDate);
            minEndDate.setDate(startDate.getDate() + 1); // +1 dia
            const minEndString = formatDate(minEndDate);

            endInput.min = minEndString;

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

    // ----------------------------
    // VALIDAÇÃO NO SUBMIT (Alerta de Exclusão Automática Removido)
    // ----------------------------
    form.addEventListener('submit', function (e) {
        // Exclusão automática (apenas fallback: limpa o campo e impede o envio)
        if (scheduledInput && expiredInput && scheduledInput.value && expiredInput.value) {
            const scheduledDate = new Date(scheduledInput.value);
            const minExpiredDate = new Date(scheduledDate.getTime() + 60000);
            const expiredDate = new Date(expiredInput.value);

            if (expiredDate < minExpiredDate) {
                // **Removido o alert() para bloquear silenciosamente no front-end**
                e.preventDefault();
                expiredInput.value = ''; // Limpa o valor inválido
                return;
            }
        }

        // Intervalo de datas
        if (startInput && endInput && startInput.value && endInput.value) {
            const startDate = new Date(startInput.value);
            const minEndDate = new Date(startDate);
            minEndDate.setDate(startDate.getDate() + 1); // +1 dia
            const endDate = new Date(endInput.value);

            if (endDate < minEndDate) {
                alert('A data final deve ser pelo menos 1 dia após a data inicial.');
                e.preventDefault();
                endInput.value = '';
                return;
            }
        }
    });

    // ----------------------------
    // PREVINE DIGITAÇÃO INVÁLIDA NO END DATE
    // ----------------------------
    if (endInput && startInput) {
        endInput.addEventListener('input', function () {
            if (!startInput.value) return;

            const startDate = new Date(startInput.value);
            const minEndDate = new Date(startDate);
            minEndDate.setDate(startDate.getDate() + 1);

            const inputDate = new Date(endInput.value);
            if (endInput.value && inputDate < minEndDate) {
                alert('A data final deve ser pelo menos 1 dia após a data inicial.');
                endInput.value = '';
            }
        });
    }
});