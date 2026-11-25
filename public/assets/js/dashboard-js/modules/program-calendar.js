/**
 * Kalendar Program & Aktiviti (FullCalendar v7, UI sahaja)
 * Elemen: #programCalendar. Pustaka dimuatkan melalui jsLibrary('dashboard', 'fullcalendar')
 * dan menyediakan objek global FullCalendar.
 * Klik acara -> popup maklumat (#eventInfoModal, lihat Program-Activity.php).
 *
 * PHP: gantikan EVENTS di bawah dengan data program daripada pangkalan data (JSON).
 */
const CATEGORIES = {
    program: { label: 'Program', color: '#386641' },
    aktiviti: { label: 'Aktiviti', color: '#2b7a9b' },
    mesyuarat: { label: 'Mesyuarat', color: '#c98a14' },
    penting: { label: 'Penting', color: '#b3402f' },
};

// Data contoh sahaja
const sample = (title, start, end, category, location, description) => ({
    title,
    start,
    ...(end ? { end } : {}),
    color: CATEGORIES[category].color,
    extendedProps: { category, location, description },
});

const EVENTS = [
    sample('Program Kitar Semula Komuniti', '2026-11-03', null, 'program', 'Dewan Komuniti Seksyen 13', 'Program kesedaran kitar semula bersama penduduk setempat.'),
    sample('Mesyuarat Jawatankuasa Sisa', '2026-11-10T10:00:00', '2026-11-10T12:00:00', 'mesyuarat', 'Bilik Mesyuarat Utama, Wisma MBSA', 'Semakan prestasi kutipan sisa bulanan.'),
    sample('Kempen Kebersihan Seksyen 7', '2026-11-14', '2026-11-16', 'aktiviti', 'Taman Rekreasi Seksyen 7', 'Kempen kebersihan tiga hari bersama sukarelawan.'),
    sample('Audit Pematuhan Kontraktor', '2026-11-19', null, 'penting', 'Pelbagai lokasi', 'Pemeriksaan pematuhan kontraktor kutipan sisa.'),
    sample('Bengkel Pengkomposan Sisa Organik', '2026-11-24T09:00:00', '2026-11-24T13:00:00', 'program', 'Dewan Seminar MBSA', 'Bengkel amali pengkomposan untuk persatuan penduduk.'),
    sample('Gotong-royong Perdana', '2026-11-28', null, 'aktiviti', 'Padang Awam Seksyen 9', 'Gotong-royong perdana peringkat bandaraya.'),
    sample('Penghantaran Laporan Suku Ke-4', '2026-12-01', null, 'penting', 'Portal ISISA', 'Tarikh akhir penghantaran laporan tonase suku ke-4.'),
    sample('Taklimat Pengurusan Sisa Industri', '2026-12-05T14:00:00', '2026-12-05T16:00:00', 'program', 'Dewan Seminar MBSA', 'Taklimat kepada pengendali sisa industri berlesen.'),
    sample('Mesyuarat Penyelarasan Meja Bantuan', '2026-12-09T09:30:00', null, 'mesyuarat', 'Bilik Mesyuarat 2', 'Penyelarasan aduan awam yang belum selesai.'),
    sample('Hari Kitar Semula Shah Alam', '2026-12-12', '2026-12-14', 'aktiviti', 'Stadium Melawati', 'Pameran dan pertandingan kitar semula tiga hari.'),
];

const DATE_FORMAT = new Intl.DateTimeFormat('ms-MY', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
const TIME_FORMAT = new Intl.DateTimeFormat('ms-MY', { hour: 'numeric', minute: '2-digit', hour12: true });

/** Tarikh (dan julat jika lebih sehari) dalam Bahasa Melayu. */
function formatDate(event) {
    const start = event.start;
    if (!event.end) return DATE_FORMAT.format(start);

    // Acara sepanjang hari: tarikh tamat FullCalendar ialah hari selepas hari terakhir
    const end = new Date(event.end);
    if (event.allDay) end.setDate(end.getDate() - 1);

    const sameDay = start.toDateString() === end.toDateString();
    return sameDay ? DATE_FORMAT.format(start) : `${DATE_FORMAT.format(start)} - ${DATE_FORMAT.format(end)}`;
}

function formatTime(event) {
    if (event.allDay) return 'Sepanjang hari';
    return event.end ? `${TIME_FORMAT.format(event.start)} - ${TIME_FORMAT.format(event.end)}` : TIME_FORMAT.format(event.start);
}

/** Isi popup maklumat dan paparkan. */
function showEventInfo(event) {
    const modalEl = document.getElementById('eventInfoModal');
    if (!modalEl || typeof window.bootstrap === 'undefined') return;

    const props = event.extendedProps || {};
    const category = CATEGORIES[props.category] || CATEGORIES.program;
    const set = (id, text) => {
        const node = document.getElementById(id);
        if (node) node.textContent = text || '-';
    };

    set('eventInfoTitle', event.title);
    set('eventInfoCategory', category.label);
    document.getElementById('eventInfoCategory')?.style.setProperty('--badge-color', category.color);
    set('eventInfoDate', formatDate(event));
    set('eventInfoTime', formatTime(event));
    set('eventInfoLocation', props.location);
    set('eventInfoDesc', props.description);

    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
}


/** Kiraan detik ke program seterusnya (data contoh daripada EVENTS). */
function initProgramCountdown() {
    const card = document.getElementById('programCountdown');
    if (!card) return;

    const now = Date.now();
    const next = EVENTS
        .map((e) => ({ ...e, time: new Date(e.start).getTime() }))
        .filter((e) => e.time > now)
        .sort((a, b) => a.time - b.time)[0];

    const set = (id, text) => {
        const node = document.getElementById(id);
        if (node) node.textContent = text;
    };

    if (!next) {
        set('countdownTitle', 'Tiada program akan datang');
        card.querySelector('.countdown-grid')?.classList.add('d-none');
        card.querySelector('#countdownCategory')?.classList.add('d-none');
        card.querySelector('#countdownDate')?.classList.add('d-none');
        return;
    }

    const category = CATEGORIES[next.extendedProps.category] || CATEGORIES.program;
    set('countdownTitle', next.title);
    set('countdownCategory', category.label);
    document.getElementById('countdownCategory')?.style.setProperty('--badge-color', category.color);
    const dateText = DATE_FORMAT.format(new Date(next.start));
    const dateNode = document.querySelector('#countdownDate span');
    if (dateNode) dateNode.textContent = dateText;

    const pad = (n) => String(n).padStart(2, '0');
    const tick = () => {
        const diff = Math.max(0, next.time - Date.now());
        const sec = Math.floor(diff / 1000);
        set('cdDays', pad(Math.floor(sec / 86400)));
        set('cdHours', pad(Math.floor((sec % 86400) / 3600)));
        set('cdMinutes', pad(Math.floor((sec % 3600) / 60)));
        set('cdSeconds', pad(sec % 60));
        return diff > 0;
    };
    if (tick()) {
        const timer = setInterval(() => { if (!tick()) clearInterval(timer); }, 1000);
    }
}

export function initProgramCalendar() {
    initProgramCountdown();

    const el = document.getElementById('programCalendar');
    if (!el || typeof window.FullCalendar === 'undefined') return;

    const calendar = new window.FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        initialDate: '2026-11-25',
        locale: 'ms',
        firstDay: 1,
        // Penuhi pembungkus (.program-calendar-wrap) yang meregang sehingga ke bawah kad
        height: '100%',
        navLinks: true,
        dayMaxEvents: true,
        headerToolbar: {
            start: 'title',
            end: 'today prev,next dayGridMonth,timeGridWeek,listMonth',
        },
        events: EVENTS,
        eventClick: (info) => showEventInfo(info.event),
    });
    calendar.render();
}
