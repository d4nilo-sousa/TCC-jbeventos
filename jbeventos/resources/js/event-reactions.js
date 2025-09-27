document.addEventListener('DOMContentLoaded', () => {
    // Usaremos '.reaction-form' como base, pois ele é o pai de todos os botões de reação
    const reactionForms = document.querySelectorAll('.reaction-form');

    // Mapeamento para textos de toggle (Salvar e Notificar)
    const toggleLabels = {
        'save': { 'added': 'Salvo', 'removed': 'Salvar' },
        'notify': { 'added': 'Notificando', 'removed': 'Notificar' }
    };

    reactionForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const url = form.action;
            const formData = new FormData(form);
            const reactionType = formData.get('reaction_type');
            
            // Seleciona o botão correto (deve ter a classe reaction-btn ou reaction-btn-toggle)
            const button = form.querySelector('.reaction-btn') || form.querySelector('.reaction-btn-toggle');
            
            // Impede cliques múltiplos e dá feedback visual
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        // Garante que o token CSRF está sendo enviado
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    // Tenta ler o erro do servidor
                    const errorData = await response.json();
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                
                // --- Lógica de Atualização Visual ---

                // 1. Lógica para CURTIR (com contagem)
                if (reactionType === 'like') {
                    const countSpan = button.querySelector('.reaction-count');

                    if (result.status === 'added') {
                        // Ativa
                        button.classList.remove('bg-white', 'text-blue-600', 'border-blue-500', 'hover:bg-blue-50');
                        button.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                        countSpan.classList.remove('bg-blue-100');
                        countSpan.classList.add('bg-white', 'text-blue-600');
                        
                        countSpan.textContent = parseInt(countSpan.textContent, 10) + 1;
                        showToast('👍 Você curtiu este evento!');

                    } else if (result.status === 'removed') {
                        // Desativa
                        button.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                        button.classList.add('bg-white', 'text-blue-600', 'border-blue-500', 'hover:bg-blue-50');
                        countSpan.classList.remove('bg-white', 'text-blue-600');
                        countSpan.classList.add('bg-blue-100');

                        countSpan.textContent = Math.max(0, parseInt(countSpan.textContent, 10) - 1);
                        showToast('👎 Você descurtiu este evento.');
                    }
                } 
                
                // 2. Lógica para SALVAR e NOTIFICAR (binário/toggle)
                else {
                    const toggleTextSpan = button.querySelector('.toggle-text');
                    const newLabel = toggleLabels[reactionType][result.status];
                    
                    if (result.status === 'added') {
                        // Ativo
                        button.classList.remove('bg-white', 'text-blue-600', 'border-blue-500', 'hover:bg-blue-50');
                        button.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                        
                        if (reactionType === 'save') showToast('💾 Evento salvo com sucesso!');
                        if (reactionType === 'notify') showToast('🔔 Você receberá notificações deste evento.');
                        
                    } else if (result.status === 'removed') {
                        // Inativo
                        button.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                        button.classList.add('bg-white', 'text-blue-600', 'border-blue-500', 'hover:bg-blue-50');
                        
                        if (reactionType === 'save') showToast('📂 Evento removido dos seus salvos.');
                        if (reactionType === 'notify') showToast('🚫 Você não receberá mais notificações deste evento.');
                    }
                    
                    // Atualiza o texto:
                    toggleTextSpan.textContent = newLabel;
                }

            } catch (error) {
                console.error('Erro ao enviar reação:', error);
                alert(`Erro ao processar sua reação. Detalhes: ${error.message}.`);
            } finally {
                // Reabilita o botão
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    });
    
    // NOTA: A função showToast() deve ser definida globalmente na view ou neste arquivo, se não estiver em outro lugar.
});