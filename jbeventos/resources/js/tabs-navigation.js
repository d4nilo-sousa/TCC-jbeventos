const tabs = document.querySelectorAll('.tab-content');
const tabButtons = document.querySelectorAll('.tab-button');
const nextButtons = document.querySelectorAll('.next-button');
const prevButtons = document.querySelectorAll('.prev-button');

function showTab(tabId) {
    tabs.forEach(tab => tab.classList.add('hidden')); 
    const activeTab = document.getElementById(tabId);
    activeTab.classList.remove('hidden'); 

    tabButtons.forEach(button => {
        const buttonSpan = button.querySelector('span:first-child');
        if (button.dataset.tabTarget === tabId) {
            button.classList.add('active', 'text-gray-700', 'font-semibold');
            buttonSpan.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-600');
            buttonSpan.classList.remove('bg-white', 'border-gray-300', 'text-gray-500');
        } else {
            button.classList.remove('active', 'text-gray-700', 'font-semibold');
            buttonSpan.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-600');
            buttonSpan.classList.add('bg-white', 'border-gray-300', 'text-gray-500');
        }
    });

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

nextButtons.forEach(button => {
    button.addEventListener('click', () => {
        const currentTab = button.closest('.tab-content');
        const nextTabId = button.dataset.nextTab;

        const inputs = currentTab.querySelectorAll('input, textarea, select');
        let allInputsValid = true;

        for (const input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                allInputsValid = false;
                break; 
            }
        }

        if (allInputsValid) {
            showTab(nextTabId);
        }
    });
});


prevButtons.forEach(button => {
    button.addEventListener('click', () => {
        const prevTabId = button.dataset.prevTab;
        showTab(prevTabId);
    });
});

tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        showTab(button.dataset.tabTarget);
    });
});

if (window.location.pathname === '/coordinator/events/create') {
    showTab('tab-media');
}