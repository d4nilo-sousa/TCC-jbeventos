function generatePassword(length = 8) {
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; // Letras maiúsculas e minúsculas
    const numbers = '0123456789'; // Dígitos numéricos
    const specialChars = '!@#$%&*'; // Caracteres especiais permitidos

    if (length < 2) length = 8; // Garante que o comprimento mínimo seja 2 (para ter ao menos número e especial)

    let passwordArray = [];

    // Garante pelo menos um caractere especial na senha
    passwordArray.push(specialChars.charAt(Math.floor(Math.random() * specialChars.length)));

    // Garante pelo menos um número
    passwordArray.push(numbers.charAt(Math.floor(Math.random() * numbers.length)));

    // Preenche o restante da senha com caracteres variados
    const allChars = letters + numbers + specialChars;
    for (let i = 2; i < length; i++) {
        passwordArray.push(allChars.charAt(Math.floor(Math.random() * allChars.length)));
    }

    // Embaralha os caracteres para evitar padrão previsível (ex: sempre número e especial no início)
    for (let i = passwordArray.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [passwordArray[i], passwordArray[j]] = [passwordArray[j], passwordArray[i]];
    }

    const password = passwordArray.join(''); // Junta os caracteres em uma única string

    document.getElementById('generated_password').value = password; // Coloca a senha gerada no campo do formulário
}

// Torna a função acessível globalmente (ex: ao clicar em um botão no HTML)
window.generatePassword = generatePassword;
