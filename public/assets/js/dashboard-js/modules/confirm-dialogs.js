/**
 * SweetAlert2: pengesahan Log Keluar, Kunci Skrin & Nyahaktifkan Akaun (Ya / Tidak)
 */
export function initConfirmDialogs() {
    // SweetAlert2: pengesahan Log Keluar, Kunci Skrin & Nyahaktifkan Akaun (Ya / Tidak)
    if (typeof Swal !== 'undefined') {
        const confirmConfigs = {
            logout: {
                icon: 'question',
                title: 'Log Keluar?',
                text: 'Adakah anda pasti mahu log keluar daripada sistem?',
                danger: true,
                // Hantar borang POST #logoutForm (ada token CSRF). Pelayan yang melaksanakan log keluar
                // dan mengalihkan pengguna ke halaman log masuk, jadi tiada mesej toastr diperlukan.
                submitForm: 'logoutForm'
            },
            lock: {
                icon: 'warning',
                title: 'Kunci Skrin?',
                text: 'Skrin akan dikunci. Anda perlu memasukkan kata laluan untuk membukanya semula.',
                danger: false,
                // Selepas "Ya", pergi ke halaman Lock-Screen (pautan pada item menu)
                goToLink: true
            },
            deactivate: {
                icon: 'warning',
                title: 'Nyahaktifkan Akaun?',
                text: 'Akaun anda akan dinyahaktifkan dan anda tidak lagi boleh log masuk. Adakah anda pasti?',
                danger: true,
                done: ['info', 'Akaun Dinyahaktifkan', 'Akaun dinyahaktifkan (placeholder, UI sahaja).']
            }
        };

        document.querySelectorAll('[data-confirm]').forEach(link => {
            link.addEventListener('click', async (e) => {
                e.preventDefault();
                const cfg = confirmConfigs[link.dataset.confirm];
                if (!cfg) return;

                const result = await Swal.fire({
                    icon: cfg.icon,
                    title: cfg.title,
                    text: cfg.text,
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak',
                    reverseButtons: true,
                    focusCancel: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-isisa',
                        confirmButton: 'swal-btn swal-btn-confirm' + (cfg.danger ? ' swal-danger' : ''),
                        cancelButton: 'swal-btn swal-btn-cancel'
                    }
                });

                // Log keluar: hantar borang ke pelayan (halaman akan beralih ke Log Masuk)
                if (result.isConfirmed && cfg.submitForm) {
                    const form = document.getElementById(cfg.submitForm);
                    if (form) form.submit();
                    return;
                }

                if (result.isConfirmed && cfg.goToLink) {
                    window.location.href = link.href;
                    return;
                }

                if (result.isConfirmed && cfg.done && typeof toastr !== 'undefined') {
                    toastr.options = {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 3000,
                        positionClass: 'toast-top-right'
                    };
                    toastr[cfg.done[0]](cfg.done[2], cfg.done[1]);
                }
            });
        });
    }
}
