document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password'); // Campo da senha principal
    const passwordConfirmation = document.getElementById('password_confirmation'); // Campo de confirmação da senha
    const requirementsList = document.getElementById('password-requirements'); // Lista com os requisitos da senha
    const errorMessage = document.getElementById('password-mismatch-error'); // Mensagem de erro caso as senhas não coincidam

    document.querySelectorAll('.toggle-password').forEach(button => {
        const input = document.querySelector(button.dataset.target); // O input de senha
        const img = button.querySelector('img'); // A imagem dentro do botão

        button.addEventListener('click', () => {
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';

            // Caminhos corretos, relativos à raiz do projeto (public/)
            img.src = isText
                ? '/imgs/blind.png'
                : '/imgs/open-eyes.png';

            img.alt = isText ? 'Ocultar senha' : 'Mostrar senha';
        });
    });



    // Validação dos requisitos da senha
    function validatePassword(password) {
        const hasLength = password.length >= 8; // Mínimo de 8 caracteres
        const hasUpper = /[A-Z]/.test(password); // Pelo menos uma letra maiúscula
        const hasNumber = /[0-9]/.test(password); // Pelo menos um número
        const hasSpecial = /[!@#$%&*]/.test(password); // Pelo menos um caractere especial

        // Altera a cor do item da lista conforme o requisito seja atendido ou não
        document.getElementById('req-length').className = hasLength ? 'text-green-600' : 'text-red-500';
        document.getElementById('req-uppercase').className = hasUpper ? 'text-green-600' : 'text-red-500';
        document.getElementById('req-number').className = hasNumber ? 'text-green-600' : 'text-red-500';
        document.getElementById('req-special').className = hasSpecial ? 'text-green-600' : 'text-red-500';

        // Retorna true se todos os requisitos forem atendidos
        return hasLength && hasUpper && hasNumber && hasSpecial;
    }

    // Validação se as senhas coincidem
    function validatePasswordsMatch() {
        if (passwordInput.value.length === 0) {
            // Se o campo estiver vazio, não mostrar erro
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
            // Senhas iguais ou confirmação vazia: sem erro
            passwordInput.style.border = '';
            passwordConfirmation.style.border = '';
            errorMessage.classList.add('hidden');
        }
    }

    // Quando o usuário foca no campo de senha, mostra os requisitos
    passwordInput.addEventListener('focus', () => {
        requirementsList.classList.remove('hidden');
    });

    // Ao digitar no campo de senha, valida os requisitos e a confirmação
    passwordInput.addEventListener('input', (e) => {
        validatePassword(e.target.value);
        validatePasswordsMatch();
    });

    // Ao digitar na confirmação, valida se as senhas coincidem
    passwordConfirmation.addEventListener('input', validatePasswordsMatch);

    // Ao sair do campo de senha, esconde a lista se tudo estiver válido
    passwordInput.addEventListener('blur', () => {
        const password = passwordInput.value.trim();
        if (password === '' || validatePassword(password)) {
            requirementsList.classList.add('hidden');
        } else {
            requirementsList.classList.remove('hidden');
        }
    });
});
