function togglePasswordVisibility() {
    var passwordInput = document.getElementById('password');
    var togglePasswordIcon = document.querySelector('.toggle-password');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        togglePasswordIcon.textContent = '🙈 Ocultar Contraseña';
    } else {
        passwordInput.type = 'password';
        togglePasswordIcon.textContent = '👁️ Ver Contraseña';
    }
}