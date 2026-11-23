/**
 * Penapisan input No. Kad Pengenalan
 * Hanya digit 0-9, maksimum 12 aksara tanpa sengkang.
 */
export function initIcInput() {
    const icInput = document.getElementById('noKadPengenalan');
    if (!icInput) return;

    icInput.addEventListener('input', function () {
        const sanitized = this.value.replace(/\D/g, '').slice(0, 12);
        if (this.value !== sanitized) {
            this.value = sanitized;
        }
    });
}
