document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-content');
    const tabButtons = document.querySelectorAll('.tab-button');
    const nextButtons = document.querySelectorAll('.next-button');
    const prevButtons = document.querySelectorAll('.prev-button');
    
    // REMOVIDA A LINHA: const form = document.getElementById('course-edit-form'); 
    // A busca do form agora é dinâmica no evento de clique do 'Próximo'.

    // === 1. Lógica de Navegação das Abas ===

    /**
     * Atualiza o estado visual do botão de aba (círculo e texto).
     * @param {HTMLElement} button O botão da aba.
     * @param {boolean} isActive Indica se a aba está ativa.
     */
    function updateTabState(button, isActive) {
        const circle = button.querySelector('span:first-child');
        const text = button.querySelector('span:last-child');
        
        const activeCircleClasses = ['border-red-500', 'bg-red-500', 'text-white']; // Ajustado para red-500
        const inactiveCircleClasses = ['border-gray-300', 'bg-white', 'text-gray-500'];
        const activeTextClasses = ['text-red-600', 'font-medium'];
        const inactiveTextClasses = ['text-gray-600']; // Ajustado para gray-600

        if (isActive) {
            circle.classList.add(...activeCircleClasses);
            circle.classList.remove(...inactiveCircleClasses);
            text.classList.add(...activeTextClasses);
            text.classList.remove(...inactiveTextClasses);
        } else {
            circle.classList.remove(...activeCircleClasses);
            circle.classList.add(...inactiveCircleClasses);
            text.classList.remove(...activeTextClasses);
            text.classList.add(...inactiveTextClasses);
        }
    }

    /**
     * Exibe a aba desejada.
     * @param {string} tabId O ID da aba a ser exibida (ex: 'tab1').
     */
    function showTab(tabId) {
        tabs.forEach(tab => tab.classList.add('hidden'));
        const activeTab = document.getElementById(tabId);
        if (activeTab) {
            activeTab.classList.remove('hidden');
        }

        tabButtons.forEach(button => {
            const isActive = button.dataset.tabTarget === tabId;
            updateTabState(button, isActive);
        });

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Navegação para a próxima aba com validação
    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            const currentTab = button.closest('.tab-content');
            const nextTabId = button.dataset.nextTab;
            let allInputsValid = true;

            // NOVA LINHA: Encontra o formulário pai mais próximo para validação.
            const form = button.closest('form');
            
            // Validação simples de campos obrigatórios
            const inputs = currentTab?.querySelectorAll('input:required, textarea:required, select:required');

            if (inputs) {
                for (const input of inputs) {
                    // Verifica se o campo está visível (offsetWidth/Height > 0) e vazio
                    if (!input.value.trim() && !input.getAttribute('disabled') && input.offsetWidth > 0 && input.offsetHeight > 0) {
                        input.focus();
                        allInputsValid = false;
                        break;
                    }
                }
            }
            
            // Força a validação nativa do HTML5, caso a validação do JS falhe ou para campos não verificados
            // Agora usa o 'form' encontrado dinamicamente.
            if (form && !form.reportValidity()) {
                 allInputsValid = false;
            }

            if (allInputsValid && nextTabId) {
                showTab(nextTabId);
            }
        });
    });

    // Navegação para a aba anterior
    prevButtons.forEach(button => {
        button.addEventListener('click', () => {
            const prevTabId = button.dataset.prevTab;
            if (prevTabId) {
                showTab(prevTabId);
            }
        });
    });

    // Navegação por clique nos botões de aba
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = button.dataset.tabTarget;
            if (target) {
                showTab(target);
            }
        });
    });

    // === 2. Lógica de Pré-visualização e Remoção de Imagens (Adaptada para Curso) ===

    /**
     * Configura a pré-visualização de arquivos para um input de arquivo único.
     * @param {string} inputId O ID do input de arquivo (ex: 'course_icon').
     * @param {string} previewId O ID do container de pré-visualização (ex: 'course_icons_preview').
     */
    function setupImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const previewContainer = document.getElementById(previewId);
        const existingPreviewId = `existing-${inputId}-preview`;

        if (input) {
            input.addEventListener('change', function() {
                previewContainer.innerHTML = ''; // Limpa previews de novas imagens
                
                // Remove o preview da imagem existente ao carregar uma nova
                const existingPreview = document.getElementById(existingPreviewId);
                if (existingPreview) {
                    existingPreview.remove(); 
                }
                
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const fileWrapper = document.createElement('div');
                        // Usamos 'new-file-preview' para identificação no JS, se necessário
                        fileWrapper.className = 'flex flex-col items-center p-2 new-file-preview'; 
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = file.name;
                        img.className = 'w-32 h-32 object-cover rounded-md border border-gray-200 shadow-sm';
                        
                        const nameSpan = document.createElement('span');
                        nameSpan.className = 'text-sm text-gray-600 mt-2 truncate max-w-full w-32 text-center';
                        nameSpan.textContent = file.name;
                        
                        fileWrapper.appendChild(img);
                        fileWrapper.appendChild(nameSpan);
                        
                        previewContainer.appendChild(fileWrapper);
                    };
                    reader.readAsDataURL(file);

                    // Se um novo arquivo foi selecionado, desmarca a remoção do existente
                    const removeInput = document.getElementById(`remove_${inputId}_input`);
                    if (removeInput) {
                        removeInput.value = '0';
                    }
                }
            });
        }
    }

    // Configuração para Ícone (single file)
    setupImagePreview('course_icon', 'course_icons_preview');
    
    // Configuração para Banner (single file)
    setupImagePreview('course_banner', 'course_banners_preview');


    // Lógica para remover imagem existente (Chama apenas em formulários de EDIÇÃO)
    // Tornamos global para ser chamado via `onclick` no Blade, como no seu original.
    window.removeExistingImage = function(button, type) {
        const container = button.closest('div');
        const removeInputId = `remove_${type}_input`; 
        const hiddenInput = document.getElementById(removeInputId);

        if (confirm(`Tem certeza que deseja remover a imagem de ${type.replace('course_', '')}?`)) {
            // Remove o container de pré-visualização da imagem existente
            container.remove();
            
            // Marca o campo oculto para indicar que a imagem deve ser removida no backend
            if (hiddenInput) {
                hiddenInput.value = '1';
            }
            
            // Opcional: Limpa o campo de input para que não envie o arquivo antigo
            const fileInput = document.getElementById(type);
            if (fileInput) {
                fileInput.value = '';
            }
        }
    }
    
    // O JS original usava um listener para a classe 'remove-existing-image-button'. 
    // Vamos reativá-lo para limpar a função global e usar o DOM, o que é mais limpo.
    document.querySelectorAll('.remove-existing-image-button').forEach(button => {
        button.addEventListener('click', function() {
            const container = this.closest('div[id^="existing-"]');
            const type = this.dataset.type; 
            const removeInputId = `remove_${type}_input`; 
            const hiddenInput = document.getElementById(removeInputId);

            if (confirm(`Tem certeza que deseja remover a imagem de ${type.replace('course_', '')}?`)) {
                // Remove o container de pré-visualização da imagem existente
                container.remove();
                
                // Marca o campo oculto para indicar que a imagem deve ser removida no backend
                if (hiddenInput) {
                    hiddenInput.value = '1';
                }
                
                // Limpa o campo de input (necessário em alguns navegadores)
                const fileInput = document.getElementById(type);
                if (fileInput) {
                    fileInput.value = '';
                }
            }
        });
    });


    // === 3. Inicialização ===

    // Inicializa a primeira aba ao carregar a página
    if (document.getElementById('tab-media')) {
        showTab('tab-media');
    } else if (document.getElementById('tab1')) {
        showTab('tab1');
    }
});