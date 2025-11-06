document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-content');
    const tabButtons = document.querySelectorAll('.tab-button');
    const nextButtons = document.querySelectorAll('.next-button');
    const prevButtons = document.querySelectorAll('.prev-button');

    // ========================
    // 1. Funções de Navegação
    // ========================

    function updateTabState(button, isActive) {
        const circle = button.querySelector('span:first-child');
        const text = button.querySelector('span:last-child');

        const activeCircleClasses = ['border-red-500', 'bg-red-500', 'text-white'];
        const inactiveCircleClasses = ['border-gray-300', 'bg-white', 'text-gray-500'];
        const activeTextClasses = ['text-red-600', 'font-medium'];
        const inactiveTextClasses = ['text-gray-600'];

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

    function showTab(tabId) {
        tabs.forEach(tab => tab.classList.add('hidden'));
        const activeTab = document.getElementById(tabId);
        if (activeTab) activeTab.classList.remove('hidden');

        tabButtons.forEach(button => {
            const isActive = button.dataset.tabTarget === tabId;
            updateTabState(button, isActive);
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateTab(currentTab) {
        const inputs = currentTab.querySelectorAll(
            'input:required:not([type="file"]), textarea:required, select:required'
        );
        for (const input of inputs) {
            if (
                !input.value.trim() &&
                !input.disabled &&
                input.offsetWidth > 0 &&
                input.offsetHeight > 0
            ) {
                input.reportValidity(); // <--- Mostra a mensagem nativa
                input.focus();
                return false;
            }
        }
        return true;
    }

    // Próximo
    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            const currentTab = button.closest('.tab-content');
            const nextTabId = button.dataset.nextTab;

            if (!currentTab) return;

            if (validateTab(currentTab) && nextTabId) {
                showTab(nextTabId);
            }
        });
    });

    // Anterior
    prevButtons.forEach(button => {
        button.addEventListener('click', () => {
            const prevTabId = button.dataset.prevTab;
            if (prevTabId) showTab(prevTabId);
        });
    });

    // Clique direto nas abas
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = button.dataset.tabTarget;
            if (target) showTab(target);
        });
    });

    function removeImage(button, type) {
        const container = button.closest('div');
        const hiddenInput = document.getElementById(`remove_${type}_input`);
        if (!container) return;

        if (confirm(`Tem certeza que deseja remover a imagem de ${type.replace('course_', '')}?`)) {
            container.remove();
            if (hiddenInput) hiddenInput.value = '1';
            const fileInput = document.getElementById(type);
            if (fileInput) fileInput.value = '';
        }
    }

    document.querySelectorAll('.remove-existing-image-button').forEach(button => {
        button.addEventListener('click', () => {
            const type = button.dataset.type;
            removeImage(button, type);
        });
    });

    // ========================
    // 3. Inicialização
    // ========================
    const firstTab = document.getElementById('tab-media') || document.getElementById('tab1');
    if (firstTab) showTab(firstTab.id);
});
