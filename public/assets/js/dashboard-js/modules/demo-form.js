/**
 * Borang contoh: toggle kata laluan, nilai slider & validasi
 */
export function initDemoForm() {
    const demoPwd = document.getElementById('inPassword');
    const demoPwdBtn = document.getElementById('demoTogglePwd');
    if (demoPwd && demoPwdBtn) {
        demoPwdBtn.addEventListener('click', () => {
            const hidden = demoPwd.type === 'password';
            demoPwd.type = hidden ? 'text' : 'password';
            demoPwdBtn.querySelector('i').className = 'bi ' + (hidden ? 'bi-eye-slash' : 'bi-eye');
            demoPwdBtn.setAttribute('aria-label', hidden ? 'Sembunyikan kata laluan' : 'Tunjukkan kata laluan');
        });
    }

    const demoRange = document.getElementById('inRange');
    const demoRangeValue = document.getElementById('rangeValue');
    if (demoRange && demoRangeValue) {
        demoRange.addEventListener('input', () => {
            demoRangeValue.textContent = demoRange.value;
        });
    }

    const demoForm = document.getElementById('demoForm');
    if (demoForm) {
        demoForm.addEventListener('submit', e => {
            e.preventDefault();
            if (!demoForm.checkValidity()) {
                demoForm.classList.add('was-validated');
                if (typeof toastr !== 'undefined') toastr.error('Sila lengkapkan medan yang diperlukan.', 'Ralat');
                return;
            }
            demoForm.classList.remove('was-validated');
            if (typeof toastr !== 'undefined') toastr.success('Borang contoh telah dihantar (UI sahaja).', 'Berjaya');
        });
        demoForm.addEventListener('reset', () => demoForm.classList.remove('was-validated'));
    }
}
