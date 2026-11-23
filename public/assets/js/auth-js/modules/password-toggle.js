/**
 * Fungsi Show / Hide Kata Laluan
 * Menukar jenis input, ikon dan atribut aksesibiliti (aria-label, aria-pressed).
 */
export function initPasswordToggle() {
    const passwordInput = document.getElementById('kataLaluan');
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    if (!(passwordInput && toggleBtn && toggleIcon)) return;

    toggleBtn.addEventListener('click', function () {
        const isHidden = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isHidden ? 'text' : 'password');

        toggleIcon.classList.toggle('bi-eye', !isHidden);
        toggleIcon.classList.toggle('bi-eye-slash', isHidden);
        toggleBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan kata laluan' : 'Tunjukkan kata laluan');
        toggleBtn.setAttribute('aria-pressed', String(isHidden));
    });
}
