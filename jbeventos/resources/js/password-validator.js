document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const requirementsList = document.getElementById('password-requirements');
    const errorMessage = document.getElementById('password-mismatch-error');

    // Se os campos de senha não existirem, encerra o script
    if (!passwordInput || !passwordConfirmation) return;

    // Tenta obter userType se existir
    const userTypeInput = document.querySelector('input[name="userType"]');
    const userType = userTypeInput ? userTypeInput.value : null;

    // Toggle show/hide password com ícone
    document.querySelectorAll('.toggle-password').forEach(button => {
        const input = document.querySelector(button.dataset.target);
        const img = button.querySelector('img');

        if (!input) return;

        button.addEventListener('click', () => {
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';

            if (img) {
                img.src = isText ? '/imgs/blind.png' : '/imgs/open-eyes.png';
                img.alt = isText ? 'Ocultar senha' : 'Mostrar senha';
            }
        });
    });

    // Validação de senha
    function validatePassword(password) {
        const hasLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%&*]/.test(password);

        const reqs = {
            length: hasLength,
            uppercase: hasUpper,
            number: hasNumber,
            special: hasSpecial
        };

        // Se lista de requisitos não existir, não faz nada
        if (!requirementsList) return true;

        // Exibe ou oculta baseando-se no userType
        if (userType === 'coordinator') {
            ['length', 'uppercase', 'number', 'special'].forEach(req => {
                const el = document.getElementById(`req-${req}`);
                if (el) {
                    el.classList.remove('hidden');
                    el.classList.toggle('text-green-600', reqs[req]);
                    el.classList.toggle('text-red-500', !reqs[req]);
                }
            });

            return hasLength && hasUpper && hasNumber && hasSpecial;
        } else {
            // Usuários comuns — apenas comprimento
            const el = document.getElementById('req-length');
            if (el) {
                el.classList.remove('hidden');
                el.classList.toggle('text-green-600', hasLength);
                el.classList.toggle('text-red-500', !hasLength);
            }

            // Oculta os outros requisitos se existirem
            ['uppercase', 'number', 'special'].forEach(req => {
                const el = document.getElementById(`req-${req}`);
                if (el) el.classList.add('hidden');
            });

            return hasLength;
        }
    }

    // Verifica se as senhas coincidem
    function validatePasswordsMatch() {
        if (!errorMessage) return;

        if (passwordInput.value.length === 0) {
            passwordInput.style.border = '';
            passwordConfirmation.style.border = '';
            errorMessage.classList.add('hidden');
            return;
        }

        if (passwordConfirmation.value.length > 0 && passwordInput.value !== passwordConfirmation.value) {
            // Senhas diferentes: borda vermelha + mensagem de erro
            passwordInput.style.border = '1px solid red';
            passwordConfirmation.style.border = '1px solid red';
            errorMessage.classList.remove('hidden');
        } else {
            passwordInput.style.border = '';
            passwordConfirmation.style.border = '';
            errorMessage.classList.add('hidden');
        }
    }

    // Mostra requisitos ao focar
    passwordInput.addEventListener('focus', () => {
        if (requirementsList) {
            requirementsList.classList.remove('hidden');
            validatePassword(passwordInput.value);
        }
    });

    // Valida ao digitar
    passwordInput.addEventListener('input', (e) => {
        const value = e.target.value;
        if (requirementsList) requirementsList.classList.remove('hidden');
        validatePassword(value);
        validatePasswordsMatch();
    });

    // Esconde requisitos ao perder foco se estiver vazio ou válido
    passwordInput.addEventListener('blur', () => {
        const isValid = validatePassword(passwordInput.value);

        if (requirementsList && (passwordInput.value.length === 0 || isValid)) {
            requirementsList.classList.add('hidden');
        }
    });

    // Valida confirmação enquanto digita
    passwordConfirmation.addEventListener('input', validatePasswordsMatch);
});
