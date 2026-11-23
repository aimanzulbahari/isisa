/**
 * Toastr: demo notifikasi & butang tindakan permohonan
 */
export function initToastrDemo() {
    // Hanya jalan jika panel demo Toastr ada pada halaman (cth: halaman Toastr), supaya halaman lain tidak ralat
    if (typeof toastr !== 'undefined' && document.getElementById('toastPosition')) {
        const toastMessages = {
            success: ['Berjaya', 'Rekod telah disimpan dengan jaya.'],
            info: ['Makluman', 'Anda mempunyai 3 permohonan baharu untuk disemak.'],
            warning: ['Amaran', 'Lesen premis akan tamat tempoh dalam 14 hari.'],
            error: ['Ralat', 'Rekod tidak dapat disimpan. Sila cuba lagi.']
        };
        const positionSelect = document.getElementById('toastPosition');
        const durationSelect = document.getElementById('toastDuration');
        const showToast = (type, title, text) => {
            const duration = parseInt(durationSelect.value, 10);
            toastr.options = {
                closeButton: document.getElementById('toastClose').checked || duration === 0,
                progressBar: document.getElementById('toastProgress').checked,
                newestOnTop: document.getElementById('toastNewest').checked,
                timeOut: duration,
                extendedTimeOut: duration === 0 ? 0 : 1500,
                tapToDismiss: true,
                positionClass: positionSelect.value
            };
            toastr[type](text, title);
        };

        document.querySelectorAll('[data-toast]').forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.dataset.toast;
                const title = document.getElementById('toastTitle').value.trim() || toastMessages[type][0];
                const text = document.getElementById('toastText').value.trim() || toastMessages[type][1];
                showToast(type, title, text);
            });
        });

        // Butang tindakan permohonan (Senarai Button) turut mencetuskan toastr
        const flowMessages = {
            hantar: ['success', 'Dihantar', 'Permohonan telah dihantar.'],
            setsemula: ['info', 'Set Semula', 'Borang telah disetkan semula.'],
            terima: ['success', 'Diterima', 'Permohonan telah diterima.'],
            tolak: ['error', 'Ditolak', 'Permohonan telah ditolak.'],
            lulus: ['success', 'Lulus', 'Permohonan telah diluluskan.'],
            gagal: ['error', 'Gagal', 'Permohonan tidak lulus semakan.'],
            penyemak: ['info', 'Dihantar', 'Permohonan dihantar kepada penyemak.'],
            pelulus: ['info', 'Dihantar', 'Permohonan dihantar kepada pelulus.']
        };
        document.querySelectorAll('[data-flow]').forEach(btn => {
            btn.addEventListener('click', () => {
                const m = flowMessages[btn.dataset.flow];
                if (m) showToast(m[0], m[1], m[2]);
            });
        });

        document.getElementById('btnClearToasts').addEventListener('click', () => toastr.clear());
    }
}
