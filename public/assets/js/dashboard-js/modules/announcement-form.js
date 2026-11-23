/**
 * Pengumuman: borang Tambah / Sunting dalam satu modal.
 *  - Butang pensil (.btn-edit-announcement) mengisi borang daripada atribut data-* pada baris itu.
 *  - Selepas modal ditutup, borang kembali ke mod "Tambah".
 *  - Jika pelayan menolak simpanan, borang dibuka semula dengan nilai yang ditaip (#announcementOld).
 * Pelayan (AnnouncementController.php) tetap mengesahkan semua input semula.
 */
export function initAnnouncementForm() {
    const modalEl = document.getElementById('announcementModal');
    const form = document.getElementById('announcementForm');
    if (!modalEl || !form) return; // Bukan halaman Pengumuman

    const heading = document.getElementById('announcementModalLabel');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    // Isi borang. Kunci sama seperti nama medan borang.
    const fill = (v) => {
        form.elements.announcement_id.value = v.announcement_id || '';
        form.elements.tajuk.value = v.tajuk || '';
        form.elements.jenis.value = v.jenis || '';
        form.elements.keterangan.value = v.keterangan || '';
        form.elements.tarikh_mula.value = v.tarikh_mula || '';
        form.elements.tarikh_tamat.value = v.tarikh_tamat || '';
        if (v.status_id) form.elements.status_id.value = v.status_id;
        heading.textContent = v.announcement_id ? 'Sunting Pengumuman' : 'Tambah Pengumuman';
    };

    // Butang Sunting
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-edit-announcement');
        if (!btn) return;
        e.preventDefault();
        const d = btn.dataset;
        fill({
            announcement_id: d.id,
            tajuk: d.tajuk,
            jenis: d.jenis,
            keterangan: d.keterangan,
            tarikh_mula: d.tarikhMula,
            tarikh_tamat: d.tarikhTamat,
            status_id: d.status
        });
        modal.show();
    });

    // Semakan ringkas sebelum hantar
    form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) {
            e.preventDefault();
            form.classList.add('was-validated');
        }
    });

    // Tutup modal: kembali ke mod Tambah
    modalEl.addEventListener('hidden.bs.modal', () => {
        form.reset();
        form.elements.announcement_id.value = '';
        heading.textContent = 'Tambah Pengumuman';
        form.classList.remove('was-validated');
    });

    // Simpanan ditolak: buka semula dengan nilai yang ditaip
    const oldEl = document.getElementById('announcementOld');
    if (oldEl) {
        try {
            fill(JSON.parse(oldEl.dataset.old || '{}'));
            modal.show();
        } catch (err) {
            // data rosak: abaikan
        }
    }
}
