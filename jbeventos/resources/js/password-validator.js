const passwordInput = document.getElementById('password');
const passwordConfirmation = document.getElementById('password_confirmation');
const requirementsList = document.getElementById('password-requirements');
const errorMessage = document.getElementById('password-mismatch-error');

// Função para validar os requisitos da senha
function validatePassword(password) {
    const hasLength = password.length >= 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[!@#$%&*]/.test(password);

    // Atualiza classes dos elementos de requisitos com base na validação
    document.getElementById('req-length').className = hasLength ? 'text-green-600' : 'text-red-500';
    document.getElementById('req-uppercase').className = hasUpper ? 'text-green-600' : 'text-red-500';
    document.getElementById('req-number').className = hasNumber ? 'text-green-600' : 'text-red-500';
    document.getElementById('req-special').className = hasSpecial ? 'text-green-600' : 'text-red-500';

    return hasLength && hasUpper && hasNumber && hasSpecial;
}

    function validatePasswordsMatch() {
    // Se senha vazia, não faz validação
    if (passwordInput.value.length === 0) {
        passwordInput.style.border = '';
        passwordConfirmation.style.border = '';
        errorMessage.classList.add('hidden');
        return;
    }

    // Se confirmação tem valor e senhas diferentes, mostra erro
    if (passwordConfirmation.value.length > 0 && passwordInput.value !== passwordConfirmation.value) {
        passwordInput.style.border = '1px solid red';
        passwordConfirmation.style.border = '1px solid red';
        errorMessage.classList.remove('hidden');
    } else {
        passwordInput.style.border = '';
        passwordConfirmation.style.border = '';
        errorMessage.classList.add('hidden');
    }
}

// Mostra lista de requisitos ao focar no campo senha
passwordInput.addEventListener('focus', () => {
    requirementsList.classList.remove('hidden');
});

// Valida requisitos e confirmação ao digitar na senha
passwordInput.addEventListener('input', (e) => {
    validatePassword(e.target.value);
    validatePasswordsMatch();
});

// Valida confirmação ao digitar nela
passwordConfirmation.addEventListener('input', validatePasswordsMatch);

// Oculta lista de requisitos se senha vazia ou válida ao perder foco
passwordInput.addEventListener('blur', () => {
    const password = passwordInput.value.trim();
    if (password === '' || validatePassword(password)) {
        requirementsList.classList.add('hidden');
    } else {
        requirementsList.classList.remove('hidden');
    }
});