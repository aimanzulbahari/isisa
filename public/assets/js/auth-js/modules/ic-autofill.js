/**
 * Isi Umur dan Tarikh Lahir (baca sahaja) daripada No. Kad Pengenalan.
 *
 * Format IC: YYMMDD-PB-###G. Hanya 2 digit tahun, jadi abad diagak sama seperti
 * core/helpers/dobHelpers.php: tahun yang lebih besar daripada tahun ini (cth: 95 pada 2026)
 * ialah 1900-an, selain itu 2000-an.
 *
 * Ini hanya untuk paparan. Pelayan perlu mengira semula nilai ini semasa pendaftaran.
 */
export function initIcAutofill() {
    const icInput = document.getElementById('noKadPengenalan');
    const ageInput = document.getElementById('umur');
    const dobInput = document.getElementById('tarikhLahir');
    if (!(icInput && ageInput && dobInput)) return;

    const pad = (n) => String(n).padStart(2, '0');

    function update() {
        const id = icInput.value.replace(/\D/g, '');
        ageInput.value = '';
        dobInput.value = '';
        if (id.length !== 12) return;

        const yy = parseInt(id.slice(0, 2), 10);
        const mm = parseInt(id.slice(2, 4), 10);
        const dd = parseInt(id.slice(4, 6), 10);

        const today = new Date();
        const thisYY = today.getFullYear() % 100;
        const year = yy > thisYY ? 1900 + yy : 2000 + yy;

        // Tarikh mesti benar (cth: 30 Feb ditolak)
        const dob = new Date(year, mm - 1, dd);
        if (dob.getFullYear() !== year || dob.getMonth() !== mm - 1 || dob.getDate() !== dd) return;

        let age = today.getFullYear() - year;
        if (today.getMonth() < mm - 1 || (today.getMonth() === mm - 1 && today.getDate() < dd)) age--;
        if (age < 0) return;

        dobInput.value = pad(dd) + '/' + pad(mm) + '/' + year;
        ageInput.value = String(age);
    }

    icInput.addEventListener('input', update);
    update();
}
