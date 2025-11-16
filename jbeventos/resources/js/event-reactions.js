document.addEventListener('DOMContentLoaded', () => {
    const reactionForms = document.querySelectorAll('.reaction-form');

    const toggleLabels = {
        'save': { 'added': 'Salvo', 'removed': 'Salvar' },
        'notify': { 'added': 'Notificado', 'removed': 'Notificar' },
        'like': { 'added': 'Curtido', 'removed': 'Curtir' }
    };

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

    const toastColors = {
        'like': 'bg-red-500',
        'save': 'bg-green-500',
        'notify': 'bg-yellow-500',
        'default': 'bg-gray-800'
    };

    function showToast(message, reactionType = 'default') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');

        toast.classList.remove(...Object.values(toastColors));
        toast.classList.add(toastColors[reactionType] || toastColors.default);

        toastMessage.textContent = message;

        toast.classList.remove('hidden');
        toast.classList.add('opacity-100');

        setTimeout(() => {
            toast.classList.add('hidden');
            toast.classList.remove('opacity-100');
        }, 3000);
    }

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

                const result = await response.json();

                button.classList.remove(...styles.active, ...styles.inactive);
                if (reactionType === 'like') {
                    const countSpan = button.querySelector('.reaction-count');
                    const icon = button.querySelector('i');
                    const toggleTextSpan = button.querySelector('.toggle-text');

                    // ---- RESET COMPLETO DE CLASSES ----
                    countSpan.className = 'reaction-count text-xs px-2 py-0.5 pt-1 rounded-full flex items-center justify-center min-w-6 h-5 transition-all';

                    // Atualiza contador conforme retorno real do backend
                    const newCount = parseInt(result.count ?? 0, 10);
                    countSpan.textContent = newCount;

                    // ---- APLICA NOVO ESTADO VISUAL ----
                    if (result.status === 'added') {
                        // Botão ativo
                        button.classList.remove(...styles.inactive);
                        button.classList.add(...styles.active);

                        // Contador ativo (fundo branco, texto vermelho + borda vermelha)
                        countSpan.classList.add('bg-white', 'text-red-500', 'border', 'border-red-500');

                        // Ícone e texto
                        icon.className = 'ph-fill ph-heart text-lg';
                        toggleTextSpan.textContent = 'Curtido';

                        showToast('👍 Você curtiu este evento!', 'like');

                    } else {
                        // Botão inativo
                        button.classList.remove(...styles.active);
                        button.classList.add(...styles.inactive);

                        // Contador inativo (fundo vermelho, texto branco)
                        countSpan.classList.add('bg-red-500', 'text-white');

                        // Ícone e texto
                        icon.className = 'ph ph-heart text-lg';
                        toggleTextSpan.textContent = 'Curtir';

                        showToast('👎 Você descurtiu este evento.', 'like');
                    }
                } else {

                    const toggleTextSpan = button.querySelector('.toggle-text');
                    const icon = button.querySelector('i');
                    const newLabel = toggleLabels[reactionType][result.status];

                    // Remove classes antigas
                    button.classList.remove(...styles.active, ...styles.inactive);

                    if (result.status === 'added') {
                        button.classList.add(...styles.active);

                        // Ícones corretos quando adiciona
                        if (reactionType === 'save') {
                            icon.className = 'ph-fill ph-bookmark-simple text-lg';
                            showToast('💾 Evento salvo!', 'save');
                        } else {
                            icon.className = 'ph-fill ph-bell-ringing text-lg';
                            showToast('🔔 Notificações ativadas!', 'notify');
                        }

                    } else {
                        button.classList.add(...styles.inactive);

                        // Ícones corretos quando remove
                        if (reactionType === 'save') {
                            icon.className = 'ph ph-bookmark-simple text-lg';
                            showToast('📂 Removido dos salvos.', 'save');
                        } else {
                            icon.className = 'ph ph-bell-ringing text-lg';
                            showToast('🚫 Notificações desativadas.', 'notify');
                        }
                    }

                    // Atualiza o texto
                    toggleTextSpan.textContent = newLabel;
                }
            } catch (error) {
                console.error('Erro ao enviar reação:', error);
                alert(`Erro: ${error.message}`);
            } finally {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    });
});
