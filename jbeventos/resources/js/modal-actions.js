document.addEventListener('DOMContentLoaded', function () {
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    // Tornando as funções acessíveis globalmente
    window.openModal = openModal;
    window.closeModal = closeModal;
});