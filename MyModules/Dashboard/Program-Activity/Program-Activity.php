<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 3) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Program &amp; Aktiviti</title>

    <!-- Favicon (see src/components/favicon.php) -->
    <?php require SRC_PATH . '/components/favicon.php'; ?>
    <!-- Font: Inter (local file, see public/library/font-library.php) -->
    <?= fontLibrary('dashboard') ?>
    <!-- Libraries: Bootstrap, Bootstrap Icons, Select2, SweetAlert2, Toastr (see public/library/css-library.php) -->
    <?= cssLibrary('dashboard', 'fullcalendar') ?>
    <!-- Page styles: must come AFTER the libraries so they can override them -->
    <link rel="stylesheet" href="<?= e(asset('assets/css/dashboard-css/main.css', true)) ?>">
</head>

<body>

    <div class="app-wrapper">
        <!-- ======================================================================== SIDEBAR ======================================================================== -->
        <!-- Sidebar Desktop + Mobile (see src/layout/sidebar.php) -->
        <?php require SRC_PATH . '/layout/sidebar.php'; ?>

        <!-- ======================================================================== MAIN ======================================================================== -->
        <div class="main-container">
            <!-- ====================================================================== HEADER ====================================================================== -->
            <?php require SRC_PATH . '/layout/header.php'; ?>

            <!-- ====================================================================== MAIN CONTENT ====================================================================== -->
            <main class="content-area">
                <section class="welcome-banner mb-4 animate-in delay-1" aria-label="Alu-aluan Pengguna">
                    <div class="position-relative" style="z-index: 1;">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>
                                <!-- PHP: Nama pengguna di bawah ialah placeholder. Gantikan dengan sesi PHP & lindungi dengan htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8') -->
                                <h2 class="welcome-title">
                                    Kalendar Program Dan Aktiviti
                                </h2>
                                <p class="mb-0 text-white text-opacity-75" style="font-size: 0.925rem;">
                                    Berikut jadual program dan aktiviti yang akan datang.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Kad Statistik (Grid: 4 lajur desktop, 2 lajur tablet, 1 lajur mobile) -->
                <!-- PHP: Gantikan nilai statistik contoh di bawah dengan data pangkalan data menggunakan htmlspecialchars() -->
                <section class="row g-3 g-lg-4 mb-4" aria-label="Ringkasan Statistik Program">
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-1">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-calendar-event" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1">48</div>
                                <div class="stat-label">Jumlah Program</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-2">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1">12</div>
                                <div class="stat-label">Program Akan Datang</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-3">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1">31</div>
                                <div class="stat-label">Program Selesai</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-4">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-people" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1">1,250</div>
                                <div class="stat-label">Jumlah Penyertaan</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Kalendar Program & Aktiviti (FullCalendar, UI sahaja: data contoh dalam program-calendar.js) -->
                <section class="row g-3 g-lg-4" aria-label="Kalendar Program Dan Aktiviti">
                    <div class="col-12 col-xl-8 animate-in delay-2">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Kalendar Program</h3>
                                    <p class="text-muted small mb-0">Paparan bulan, minggu dan senarai bagi program serta aktiviti</p>
                                </div>
                                <!-- Petunjuk kategori -->
                                <ul class="calendar-legend list-unstyled mb-0" aria-label="Petunjuk kategori">
                                    <li><span class="legend-dot" style="--dot: #386641;"></span>Program</li>
                                    <li><span class="legend-dot" style="--dot: #2b7a9b;"></span>Aktiviti</li>
                                    <li><span class="legend-dot" style="--dot: #c98a14;"></span>Mesyuarat</li>
                                    <li><span class="legend-dot" style="--dot: #b3402f;"></span>Penting</li>
                                </ul>
                            </div>

                            <!-- PHP: Gantikan data contoh dengan rekod program daripada pangkalan data -->
                            <!-- Pembungkus meregang sehingga ke bawah kad; kalendar mengisi ruang ini -->
                            <div class="program-calendar-wrap">
                                <div id="programCalendar" class="program-calendar" aria-label="Kalendar Program Dan Aktiviti"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad Pautan Pantas (sebelah kanan kalendar) -->
                    <div class="col-12 col-xl-4 d-flex flex-column gap-3 gap-lg-4 animate-in delay-3">
                        <div class="isisa-card p-3 p-md-4">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Pautan Pantas</h3>
                                <p class="text-muted small mb-0">Capaian pantas ke modul yang kerap digunakan</p>
                            </div>
                            <!-- PHP: Gantikan href dengan laluan modul sebenar -->
                            <nav class="d-flex flex-column gap-2" aria-label="Pautan Pantas">
                                <a href="<?= e(routeUrl('broadcast-program')) ?>" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-calendar-event" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Senarai Program</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-plus-circle" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Tambah Program Baharu</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                                <a href="<?= e(routeUrl('announcement')) ?>" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-megaphone" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Pengumuman</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Laporan Program</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-question-circle" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Bantuan &amp; Panduan</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                            </nav>
                        </div>

                        <!-- Kad Kiraan Detik: program seterusnya (diisi oleh program-calendar.js, UI sahaja) -->
                        <div class="isisa-card p-3 p-md-4 countdown-card" id="programCountdown">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Program Seterusnya</h3>
                                <p class="text-muted small mb-0">Kiraan detik ke program yang akan datang</p>
                            </div>
                            <div class="countdown-event">
                                <span class="event-info-badge" id="countdownCategory">Program</span>
                                <div class="countdown-title" id="countdownTitle">-</div>
                                <div class="countdown-date" id="countdownDate"><i class="bi bi-calendar-event" aria-hidden="true"></i><span>-</span></div>
                            </div>
                            <div class="countdown-grid" role="timer" aria-live="off" aria-label="Kiraan detik">
                                <div class="countdown-box"><span class="countdown-num tabular-nums" id="cdDays">00</span><span class="countdown-unit">Hari</span></div>
                                <div class="countdown-box"><span class="countdown-num tabular-nums" id="cdHours">00</span><span class="countdown-unit">Jam</span></div>
                                <div class="countdown-box"><span class="countdown-num tabular-nums" id="cdMinutes">00</span><span class="countdown-unit">Minit</span></div>
                                <div class="countdown-box"><span class="countdown-num tabular-nums" id="cdSeconds">00</span><span class="countdown-unit">Saat</span></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Carta -->
                <section class="row g-3 g-lg-4 mt-0" aria-label="Carta Program Dan Aktiviti">
                    <div class="col-12 col-lg-4 animate-in delay-4">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Jenis Permohonan Teratas</h3>
                                <p class="text-muted small mb-0">Lima kategori dengan permohonan terbanyak</p>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 300px;">
                                <canvas id="isisaHorizontalBar" aria-label="Carta Bar Mendatar Jenis Permohonan" role="img"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Aduan Mengikut Zon</h3>
                                <p class="text-muted small mb-0">Bilangan aduan awam bagi setiap zon (polar area)</p>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 300px;">
                                <canvas id="isisaPolarArea" aria-label="Carta Polar Area Aduan Mengikut Zon" role="img"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Prestasi Perkhidmatan</h3>
                                <p class="text-muted small mb-0">Perbandingan skor prestasi 2025 dan 2026 (radar)</p>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 300px;">
                                <canvas id="isisaRadar" aria-label="Carta Radar Prestasi Perkhidmatan" role="img"></canvas>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <!-- ======================================================================
           BAHAGIAN 4: FOOTER (Boleh diasingkan kepada includes/footer.php)
           ====================================================================== -->
            <?php require SRC_PATH . '/layout/footer.php'; ?>

        </div>
    </div>

    <!-- Popup Maklumat Program (dibuka bila acara dalam kalendar diklik; diisi oleh program-calendar.js) -->
    <div class="modal fade" id="eventInfoModal" tabindex="-1" aria-labelledby="eventInfoTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <span class="event-info-badge" id="eventInfoCategory">Program</span>
                        <h2 class="modal-title h5 fw-bold mt-2" id="eventInfoTitle">Tajuk Program</h2>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <dl class="event-info-list mb-0">
                        <div class="event-info-row">
                            <dt><i class="bi bi-calendar-event" aria-hidden="true"></i><span>Tarikh</span></dt>
                            <dd id="eventInfoDate">-</dd>
                        </div>
                        <div class="event-info-row">
                            <dt><i class="bi bi-clock" aria-hidden="true"></i><span>Masa</span></dt>
                            <dd id="eventInfoTime">-</dd>
                        </div>
                        <div class="event-info-row">
                            <dt><i class="bi bi-geo-alt" aria-hidden="true"></i><span>Lokasi</span></dt>
                            <dd id="eventInfoLocation">-</dd>
                        </div>
                        <div class="event-info-row">
                            <dt><i class="bi bi-text-paragraph" aria-hidden="true"></i><span>Keterangan</span></dt>
                            <dd id="eventInfoDesc">-</dd>
                        </div>
                    </dl>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Butang Kembali ke Atas -->
    <button type="button" class="back-to-top" id="backToTop" aria-label="Kembali ke atas" title="Kembali ke atas">
        <i class="bi bi-arrow-up" aria-hidden="true"></i>
    </button>

    <!-- Libraries: jQuery, Bootstrap bundle, Axios, Chart.js, Select2, SweetAlert2, Toastr (see public/library/js-library.php) -->
    <?= jsLibrary('dashboard', 'fullcalendar') ?>
    <!-- Page scripts: type="module" because main.js imports its modules. Must come AFTER the libraries. -->
    <script type="module" src="<?= e(asset('assets/js/dashboard-js/main.js', true)) ?>"></script>


</body>

</html>