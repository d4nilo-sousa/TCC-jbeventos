const passwordInput = document.getElementById('password');
const requirementsList = document.getElementById('password-requirements');

function validatePassword(password) {
    const hasLength = password.length >= 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[!@#$%&*]/.test(password);

    document.getElementById('req-length').className = hasLength ? 'text-green-600' : 'text-red-500';
    document.getElementById('req-uppercase').className = hasUpper ? 'text-green-600' : 'text-red-500';
    document.getElementById('req-number').className = hasNumber ? 'text-green-600' : 'text-red-500';
    document.getElementById('req-special').className = hasSpecial ? 'text-green-600' : 'text-red-500';

    return hasLength && hasUpper && hasNumber && hasSpecial;
}

// Mostra a lista quando o campo recebe foco
passwordInput.addEventListener('focus', () => {
    requirementsList.classList.remove('hidden');
});

// Valida enquanto o usuário digita
passwordInput.addEventListener('input', (e) => {
    validatePassword(e.target.value);
});

// Ao sair do campo (blur), oculta a lista se o campo estiver vazio ou se a senha for válida
passwordInput.addEventListener('blur', () => {
    const password = passwordInput.value.trim();
    if (password === '' || validatePassword(password)) {
        requirementsList.classList.add('hidden');
    } else {
        requirementsList.classList.remove('hidden'); // Opcional: mantém a lista se inválida
    }
});
