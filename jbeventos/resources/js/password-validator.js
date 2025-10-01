document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const requirementsList = document.getElementById('password-requirements');
    const errorMessage = document.getElementById('password-mismatch-error');

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
                img.alt = isText ? 'Mostrar senha' : 'Ocultar senha';
            }
        });
    });

    // Se não tiver confirmação nem requisitos (caso do LOGIN), para aqui
    if (!passwordConfirmation && !requirementsList) return;

    // --- Daqui pra baixo é só pra telas de CADASTRO/RESET DE SENHA ---
    const userTypeInput = document.querySelector('input[name="userType"]');
    const userType = userTypeInput ? userTypeInput.value : null;

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

        if (!requirementsList) return true;

        if (userType === 'coordinator' || userType === 'admin') {
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
            const el = document.getElementById('req-length');
            if (el) {
                el.classList.remove('hidden');
                el.classList.toggle('text-green-600', hasLength);
                el.classList.toggle('text-red-500', !hasLength);
            }
            ['uppercase', 'number', 'special'].forEach(req => {
                const el = document.getElementById(`req-${req}`);
                if (el) el.classList.add('hidden');
            });
            return hasLength;
        }
    }

    function validatePasswordsMatch() {
        if (!errorMessage || !passwordConfirmation) return;

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

    if (passwordInput) {
        passwordInput.addEventListener('focus', () => {
            if (requirementsList) {
                requirementsList.classList.remove('hidden');
                validatePassword(passwordInput.value);
            }
        });

        passwordInput.addEventListener('input', (e) => {
            const value = e.target.value;
            if (requirementsList) requirementsList.classList.remove('hidden');
            validatePassword(value);
            validatePasswordsMatch();
        });

        passwordInput.addEventListener('blur', () => {
            const isValid = validatePassword(passwordInput.value);
            if (requirementsList && (passwordInput.value.length === 0 || isValid)) {
                requirementsList.classList.add('hidden');
            }
        });
    }

    if (passwordConfirmation) {
        passwordConfirmation.addEventListener('input', validatePasswordsMatch);
    }
});
