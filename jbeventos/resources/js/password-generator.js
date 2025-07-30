function generatePassword(length = 8) {
    // Conjunto de caracteres permitidos (letras maiúsculas, minúsculas, números e caracteres especiais)
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&*';
    let password = '';
    
    // Gera a senha caractere por caractere
    for (let i = 0; i < length; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    // Define o valor gerado no campo de input com id "generated_password"
    document.getElementById('generated_password').value = password;
}

// Torna a função acessível globalmente para funcionar com eventos como onclick no HTML
window.generatePassword = generatePassword;
