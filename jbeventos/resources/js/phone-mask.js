document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('phone_number'); // Campo de número de telefone

    // Função para aplicar a máscara no formato (99) 99999-9999
    function maskPhone(value) {
        value = value.replace(/\D/g, ''); // Remove tudo que não for dígito

        if (value.length > 11) value = value.slice(0, 11); // Limita a 11 dígitos

        if (value.length > 6) {
            // Aplica máscara completa: (99) 99999-9999
            return `(${value.slice(0, 2)}) ${value.slice(2, 7)}-${value.slice(7)}`;
        } else if (value.length > 2) {
            // Aplica máscara parcial: (99) 99999...
            return `(${value.slice(0, 2)}) ${value.slice(2)}`;
        } else if (value.length > 0) {
            // Início do preenchimento: (
            return `(${value}`;
        }
        return '';
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            e.target.value = maskPhone(e.target.value); // Aplica a máscara a cada digitação
        });
    }
});
