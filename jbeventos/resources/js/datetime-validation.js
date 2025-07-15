// Aguarda o carregamento completo do DOM antes de executar o código
document.addEventListener('DOMContentLoaded', function () {
    console.log('JS carregado');

    // Captura os campos de data de agendamento e expiração, e o formulário
    const scheduledInput = document.getElementById('event_scheduled_at');
    const expiredInput = document.getElementById('event_expired_at');
    const form = document.querySelector('form');

    // Função auxiliar que adiciona zero à esquerda se necessário (ex: 5 -> "05")
    function pad(num) {
        return num.toString().padStart(2, '0');
    }

    // Calcula a data mínima para o campo de expiração: 1 minuto após a data de agendamento
    function getMinExpiredDateString(scheduledDate) {
        const minExpiredDate = new Date(scheduledDate.getTime() + 60000); // 60.000 ms = 1 min
        const year = minExpiredDate.getFullYear();
        const month = pad(minExpiredDate.getMonth() + 1); // mês começa em 0
        const day = pad(minExpiredDate.getDate());
        const hours = pad(minExpiredDate.getHours());
        const minutes = pad(minExpiredDate.getMinutes());
        return `${year}-${month}-${day}T${hours}:${minutes}`; // formato para input datetime-local
    }

    // Atualiza o valor mínimo permitido no campo de expiração sempre que o agendamento mudar
    function updateExpiredMin() {
        if (scheduledInput.value) {
            const scheduledDate = new Date(scheduledInput.value);
            const minString = getMinExpiredDateString(scheduledDate);
            expiredInput.min = minString;

            // Se o valor atual de expiração for inválido, ele é limpo
            if (expiredInput.value && expiredInput.value < minString) {
                expiredInput.value = '';
            }
        }
    }

    // Valida o campo de expiração ao digitar: precisa ser 1 minuto após o agendamento
    expiredInput.addEventListener('input', function () {
        if (scheduledInput.value && expiredInput.value) {
            const scheduledDate = new Date(scheduledInput.value);
            const minExpiredDate = new Date(scheduledDate.getTime() + 60000);
            const expiredDate = new Date(expiredInput.value);

            if (expiredDate < minExpiredDate) {
                alert('O horário de encerramento deve ser pelo menos 1 minuto após o agendamento.');
                expiredInput.value = '';
            }
        }
    });

    // Atualiza a data mínima de expiração sempre que o campo de agendamento for alterado
    scheduledInput.addEventListener('change', updateExpiredMin);

    // Validação final ao enviar o formulário
    form.addEventListener('submit', function (event) {
        if (scheduledInput.value && expiredInput.value) {
            const scheduledDate = new Date(scheduledInput.value);
            const minExpiredDate = new Date(scheduledDate.getTime() + 60000);
            const expiredDate = new Date(expiredInput.value);

            if (expiredDate < minExpiredDate) {
                alert('O horário de encerramento deve ser pelo menos 1 minuto após o agendamento.');
                event.preventDefault(); // impede o envio do formulário
            }
        }
    });
});
