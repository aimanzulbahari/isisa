/**
 * Mesej daripada pelayan sebagai popup (SweetAlert2)
 *
 * PHP mencetak mesej dalam elemen #serverNotice (contohnya "No. Kad Pengenalan atau
 * kata laluan tidak betul."), dengan atribut:
 *   data-type    : danger | warning | info | success
 *   data-message : teks mesej
 *
 * Jika SweetAlert2 dimuatkan, mesej dipaparkan sebagai popup dan kotak mesej biasa
 * disembunyikan. Jika tidak (contohnya JavaScript gagal dimuatkan), kotak mesej
 * biasa kekal kelihatan, jadi pengguna tetap nampak mesej tersebut.
 */
const ICONS = {
    danger: 'error',
    warning: 'warning',
    info: 'info',
    success: 'success',
};

const TITLES = {
    danger: 'Log Masuk Gagal',
    warning: 'Perhatian',
    info: 'Makluman',
    success: 'Berjaya',
};

/** Escape text before it goes into the popup HTML (the values come from the server). */
function esc(value) {
    const div = document.createElement('div');
    div.textContent = String(value ?? '');
    return div.innerHTML;
}

/**
 * Popup Penyelenggaraan: dipaparkan apabila log masuk ditolak kerana sistem dalam penyelenggaraan.
 * Data datang daripada pelayan (atribut data-maintenance pada #serverNotice, lihat
 * maintenanceDetails() dalam core/helpers/maintenanceHelpers.php):
 *   { message, start, end, officer }
 * Baris Mula / Tamat / Pegawai yang kosong tidak dipaparkan.
 */
function showMaintenancePopup(data) {
    const rows = [
        ['bi-calendar-event', 'Mula', data.start],
        ['bi-calendar-check', 'Tamat', data.end],
        ['bi-person-vcard', 'Pegawai', data.officer],
    ].filter(([, , value]) => value);

    const info = rows.length === 0 ? '' : `
            <dl class="maint-info">${rows.map(([icon, label, value]) => `
                <div class="maint-row">
                    <dt><i class="bi ${icon}" aria-hidden="true"></i><span>${label}</span></dt>
                    <dd>${esc(value)}</dd>
                </div>`).join('')}
            </dl>`;

    return window.Swal.fire({
        iconHtml: '<i class="bi bi-tools" aria-hidden="true"></i>',
        title: 'Sistem Dalam Penyelenggaraan',
        html: `<p class="maint-message">${esc(data.message)}</p>${info}`,
        confirmButtonText: 'Faham',
        buttonsStyling: false,
        returnFocus: false,
        customClass: {
            popup: 'swal-isisa swal-maintenance',
            icon: 'maint-icon',
            confirmButton: 'swal-btn swal-btn-confirm',
        },
    });
}

export function initNoticePopup() {
    const notice = document.getElementById('serverNotice');
    if (!notice || typeof window.Swal === 'undefined') return;

    const type = notice.dataset.type in ICONS ? notice.dataset.type : 'info';
    const message = notice.dataset.message || notice.textContent.trim();
    if (!message) return;

    notice.classList.add('d-none'); // Popup menggantikan kotak mesej biasa

    // Log masuk ditolak kerana penyelenggaraan: popup Penyelenggaraan dengan data sebenar
    let maintenance = null;
    if (notice.dataset.maintenance) {
        try {
            maintenance = JSON.parse(notice.dataset.maintenance);
        } catch {
            maintenance = null; // Data rosak: papar popup biasa dengan mesej sahaja
        }
    }

    // Selain itu: rupa popup sama dengan popup "Log Keluar?" pada dashboard
    // (kelas swal-isisa dan swal-btn, lihat auth-css/components/sweetalert.css)
    const popup = maintenance ? showMaintenancePopup(maintenance) : window.Swal.fire({
        icon: ICONS[type],
        title: TITLES[type],
        text: message,
        confirmButtonText: 'OK',
        buttonsStyling: false,
        returnFocus: false,
        customClass: {
            popup: 'swal-isisa',
            confirmButton: 'swal-btn swal-btn-confirm',
        },
    });

    popup.then(() => {
        // Letak kursor pada medan yang perlu diisi semula:
        // kata laluan jika No. KP sudah ada, jika tidak No. KP.
        const ic = document.getElementById('noKadPengenalan');
        const pw = document.getElementById('kataLaluan');
        const target = ic && ic.value.trim() !== '' ? pw : ic;
        if (target) target.focus();
    });
}
