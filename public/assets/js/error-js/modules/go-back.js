/**
 * Butang "Halaman Sebelumnya"
 * Kembali ke sejarah pelayar jika ada, atau terus ke halaman log masuk.
 * Alamat log masuk diambil daripada atribut data-fallback pada butang
 * (ditetapkan oleh PHP), kerana laluan relatif berbeza mengikut halaman.
 */
export function initGoBack() {
    const backBtn = document.getElementById('btnGoBack');
    if (!backBtn) return;

    backBtn.addEventListener('click', function () {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = backBtn.dataset.fallback || 'login.php';
        }
    });
}
