document.addEventListener('DOMContentLoaded', function () {
    const userType = document.querySelector('input[name="userType"]').value;

    const passwordInput = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const requirementsList = document.getElementById('password-requirements');
    const errorMessage = document.getElementById('password-mismatch-error');

    // Toggle show/hide password
    document.querySelectorAll('.toggle-password').forEach(button => {
        const input = document.querySelector(button.dataset.target);
        const img = button.querySelector('img');

        button.addEventListener('click', () => {
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            img.src = isText ? '/imgs/blind.png' : '/imgs/open-eyes.png';
            img.alt = isText ? 'Ocultar senha' : 'Mostrar senha';
        });
    });

    function validatePassword(password) {
        const hasLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%&*]/.test(password);

        if (userType === 'coordinator') {
            ['length', 'uppercase', 'number', 'special'].forEach(req => {
                document.getElementById(`req-${req}`).classList.remove('hidden');
            });

            document.getElementById('req-length').classList.toggle('text-green-600', hasLength);
            document.getElementById('req-length').classList.toggle('text-red-500', !hasLength);

            document.getElementById('req-uppercase').classList.toggle('text-green-600', hasUpper);
            document.getElementById('req-uppercase').classList.toggle('text-red-500', !hasUpper);

            document.getElementById('req-number').classList.toggle('text-green-600', hasNumber);
            document.getElementById('req-number').classList.toggle('text-red-500', !hasNumber);

            document.getElementById('req-special').classList.toggle('text-green-600', hasSpecial);
            document.getElementById('req-special').classList.toggle('text-red-500', !hasSpecial);

            return hasLength && hasUpper && hasNumber && hasSpecial;
        } else {
            document.getElementById('req-length').classList.remove('hidden');
            document.getElementById('req-length').classList.toggle('text-green-600', hasLength);
            document.getElementById('req-length').classList.toggle('text-red-500', !hasLength);

            document.getElementById('req-uppercase').classList.add('hidden');
            document.getElementById('req-number').classList.add('hidden');
            document.getElementById('req-special').classList.add('hidden');

            return hasLength;
        }
    }

    function validatePasswordsMatch() {
        if (passwordInput.value.length === 0) {
            passwordInput.style.border = '';
            passwordConfirmation.style.border = '';
            errorMessage.classList.add('hidden');
            return;
        }

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

    // Sempre mostra requisitos ao focar (mesmo vazio)
    passwordInput.addEventListener('focus', () => {
        requirementsList.classList.remove('hidden');
        validatePassword(passwordInput.value);
    });

    // Valida enquanto digita
    passwordInput.addEventListener('input', (e) => {
        const value = e.target.value;
        requirementsList.classList.remove('hidden'); // sempre mostra
        validatePassword(value);
        validatePasswordsMatch();
    });

    // Esconde requisitos ao perder foco se campo vazio ou senha válida
    passwordInput.addEventListener('blur', () => {
        const isValid = validatePassword(passwordInput.value);

        if (passwordInput.value.length === 0 || isValid) {
            requirementsList.classList.add('hidden');
        } 
        // se inválida e campo não vazio, mantém visível
    });

    passwordConfirmation.addEventListener('input', validatePasswordsMatch);
});
