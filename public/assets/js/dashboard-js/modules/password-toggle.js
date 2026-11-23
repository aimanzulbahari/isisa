/**
 * Butang mata pada medan kata laluan (tunjuk / sembunyikan)
 * Penggunaan: <button type="button" data-toggle-password="idInput"><i class="bi bi-eye"></i></button>
 */
export function initPasswordToggle() {
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        const input = document.getElementById(btn.dataset.togglePassword);
        if (!input) return;

        btn.addEventListener('click', () => {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';

            const icon = btn.querySelector('i');
            if (icon) icon.className = 'bi ' + (show ? 'bi-eye-slash' : 'bi-eye');

            btn.setAttribute('aria-label', show ? 'Sembunyikan kata laluan' : 'Tunjukkan kata laluan');
            btn.setAttribute('aria-pressed', String(show));
        });
    });
}
