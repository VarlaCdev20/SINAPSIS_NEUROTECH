// login.js — controla interacciones específicas de la pantalla de login
document.addEventListener('DOMContentLoaded', () => {
    const tp = document.getElementById('togglePassword');
    const pw = document.getElementById('password');

    if (!tp || !pw) return;

    tp.addEventListener('click', () => {
        const isPass = pw.type === 'password';
        pw.type = isPass ? 'text' : 'password';
        tp.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
});
