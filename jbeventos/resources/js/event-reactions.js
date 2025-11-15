document.addEventListener('DOMContentLoaded', () => {
    const reactionForms = document.querySelectorAll('.reaction-form');

    const toggleLabels = {
        'save': { 'added': 'Salvo', 'removed': 'Salvar' },
        'notify': { 'added': 'Notificado', 'removed': 'Notificar' },
        'like': { 'added': 'Curtido', 'removed': 'Curtir' }
    };

    // Estilos para cada tipo de reação
    const reactionStyles = {
        'like': {
            active: ['bg-red-500', 'text-white', 'border-red-500', 'hover:bg-red-600'],
            inactive: ['bg-white', 'text-gray-700', 'border-gray-300', 'hover:bg-gray-50'],
            countActive: ['bg-white', 'text-red-500'],
            countInactive: ['bg-gray-200', 'text-gray-700']
        },
        'save': {
            active: ['bg-green-500', 'text-white', 'border-green-500', 'hover:bg-green-600'],
            inactive: ['bg-white', 'text-green-600', 'border-green-300', 'hover:bg-green-50']
        },
        'notify': {
            active: ['bg-yellow-500', 'text-gray-900', 'border-yellow-500', 'hover:bg-yellow-600'],
            inactive: ['bg-white', 'text-yellow-600', 'border-yellow-300', 'hover:bg-yellow-50']
        }
    };

    // Mapeia cores do toast conforme tipo
    const toastColors = {
        'like': 'bg-red-500',
        'save': 'bg-green-500',
        'notify': 'bg-yellow-500',
        'default': 'bg-gray-800'
    };

    // Função para exibir toast com cor por tipo
    function showToast(message, reactionType = 'default') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');

        // Limpa classes antigas de background
        toast.classList.remove(...Object.values(toastColors));

        // Aplica nova cor
        toast.classList.add(toastColors[reactionType] || toastColors.default);

        // Define a mensagem
        toastMessage.textContent = message;

        // Exibe
        toast.classList.remove('hidden');
        toast.classList.add('opacity-100');

        // Esconde após 3s
        setTimeout(() => {
            toast.classList.add('hidden');
            toast.classList.remove('opacity-100');
        }, 3000);
    }

    // Manipulador principal
    reactionForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const url = form.action;
            const formData = new FormData(form);
            const reactionType = formData.get('reaction_type');
            const styles = reactionStyles[reactionType];
            const button = form.querySelector('.reaction-btn') || form.querySelector('.reaction-btn-toggle');

            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Limpa classes anteriores
                button.classList.remove(...styles.active, ...styles.inactive);

                if (reactionType === 'like') {
                    const countSpan = button.querySelector('.reaction-count');
                    countSpan.classList.remove(...styles.countActive, ...styles.countInactive);

                    let currentCount = parseInt(countSpan.textContent, 10);
                    if (isNaN(currentCount)) currentCount = 0;

                    if (result.status === 'added') {
                        button.classList.add(...styles.active);
                        countSpan.classList.add(...styles.countActive);
                        countSpan.textContent = currentCount + 1;
                        showToast('👍 Você curtiu este evento!', 'like');
                    } else {
                        button.classList.add(...styles.inactive);
                        countSpan.classList.add(...styles.countInactive);
                        countSpan.textContent = Math.max(0, currentCount - 1);
                        showToast('👎 Você descurtiu este evento.', 'like');
                    }

                } else {
                    const toggleTextSpan = button.querySelector('.toggle-text');
                    const newLabel = toggleLabels[reactionType][result.status];

                    if (result.status === 'added') {
                        button.classList.add(...styles.active);
                        showToast(
                            reactionType === 'save' ? '💾 Evento salvo com sucesso!' : '🔔 Você receberá notificações deste evento.',
                            reactionType
                        );
                    } else {
                        button.classList.add(...styles.inactive);
                        showToast(
                            reactionType === 'save' ? '📂 Evento removido dos seus salvos.' : '🚫 Você não receberá mais notificações deste evento.',
                            reactionType
                        );
                    }

                    toggleTextSpan.textContent = newLabel;
                }

            } catch (error) {
                console.error('Erro ao enviar reação:', error);
                alert(`Erro ao processar sua reação. Detalhes: ${error.message}.`);
            } finally {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    });
});
