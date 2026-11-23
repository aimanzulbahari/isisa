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
    <title>Card Template</title>

    <!-- Favicon (see src/components/favicon.php) -->
    <?php require SRC_PATH . '/components/favicon.php'; ?>
    <!-- Font: Inter (local file, see public/library/font-library.php) -->
    <?= fontLibrary('dashboard') ?>
    <!-- Libraries: Bootstrap, Bootstrap Icons, Select2, SweetAlert2, Toastr (see public/library/css-library.php) -->
    <?= cssLibrary('dashboard') ?>
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
                <!-- BREADCRUMBS -->
                <?php require SRC_PATH . '/components/breadcrumbs.php'; ?>

                <!-- 2. Welcome Header / Banner Alu-aluan -->
                <?php require SRC_PATH . '/components/welcome-header.php'; ?>

                <!-- 3. 4 Kad Statistik (Grid: 4 lajur desktop, 2 lajur tablet, 1 lajur mobile) -->
                <!-- PHP: Gantikan nilai statistik contoh di bawah dengan data pangkalan data menggunakan htmlspecialchars() -->
                <section class="row g-3 g-lg-4 mb-4" aria-label="Ringkasan Statistik">
                    <!-- Kad 1 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-1">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-folder2-open" aria-hidden="true"></i>
                                </div>
                                <span class="stat-trend trend-up">
                                    <i class="bi bi-arrow-up-short" aria-hidden="true"></i>+12.4%
                                </span>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1">1,284</div>
                                <div class="stat-label">Jumlah Rekod Aktif</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad 2 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-2">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                                </div>
                                <span class="stat-trend trend-warn">
                                    <i class="bi bi-clock" aria-hidden="true"></i>48 baru
                                </span>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1">142</div>
                                <div class="stat-label">Permohonan Dalam Proses</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad 3 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-3">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                </div>
                                <span class="stat-trend trend-up">
                                    <i class="bi bi-arrow-up-short" aria-hidden="true"></i>+8.2%
                                </span>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1">964</div>
                                <div class="stat-label">Selesai Bulan Ini</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad 4 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-4">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-building" aria-hidden="true"></i>
                                </div>
                                <span class="stat-trend trend-up">
                                    <i class="bi bi-check" aria-hidden="true"></i>Aktif
                                </span>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1">24</div>
                                <div class="stat-label">Jabatan Berdaftar</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 8. Komponen: Accordion -->
                <section class="row g-3 g-lg-4" aria-label="Accordion">

                    <!-- Accordion Biasa (satu panel dibuka pada satu masa) -->
                    <div class="col-12 col-lg-6 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Accordion</h3>
                                <p class="text-muted small mb-0">Soalan lazim — satu panel dibuka pada satu masa</p>
                            </div>

                            <div class="accordion accordion-isisa" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                                            <i class="bi bi-file-earmark-plus acc-icon" aria-hidden="true"></i>Bagaimana membuat permohonan baharu?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">Pilih menu <strong>Permohonan</strong>, klik <strong>Tambah Data</strong>, lengkapkan borang dan tekan <strong>Simpan</strong>. Status permohonan boleh dipantau dalam jadual Aktiviti Terkini.</div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                            <i class="bi bi-clock-history acc-icon" aria-hidden="true"></i>Berapa lama tempoh semakan permohonan?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">Tempoh semakan biasa ialah 7 hari bekerja selepas dokumen lengkap diterima oleh jabatan berkaitan.</div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                            <i class="bi bi-key acc-icon" aria-hidden="true"></i>Bagaimana menetapkan semula kata laluan?
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">Di halaman log masuk, klik <strong>Lupa kata laluan?</strong> dan ikut arahan yang dihantar ke emel berdaftar anda.</div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                                            <i class="bi bi-headset acc-icon" aria-hidden="true"></i>Siapa boleh dihubungi untuk bantuan?
                                        </button>
                                    </h2>
                                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">Hubungi Jabatan Kesihatan dan Kelestarian Sisa, MBSA semasa waktu pejabat, Isnin hingga Jumaat.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion Terbuka Serentak -->
                    <div class="col-12 col-lg-6 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Accordion Berbilang Panel</h3>
                                <p class="text-muted small mb-0">Beberapa panel boleh dibuka serentak, dengan lencana ringkas</p>
                            </div>

                            <div class="accordion accordion-isisa" id="detailAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#acc1" aria-expanded="true" aria-controls="acc1">
                                            <span class="flex-grow-1">Maklumat Premis</span>
                                            <span class="badge-status badge-status-success me-2">Lengkap</span>
                                        </button>
                                    </h2>
                                    <div id="acc1" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <dl class="row mb-0 small">
                                                <dt class="col-5 text-muted fw-medium">Nama Premis</dt>
                                                <dd class="col-7">Restoran Seri Kenanga</dd>
                                                <dt class="col-5 text-muted fw-medium">No. Lesen</dt>
                                                <dd class="col-7 mb-0">MBSA-2026-01482</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc2" aria-expanded="false" aria-controls="acc2">
                                            <span class="flex-grow-1">Dokumen Sokongan</span>
                                            <span class="badge-status badge-status-warning me-2">2 Belum</span>
                                        </button>
                                    </h2>
                                    <div id="acc2" class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled mb-0 small d-flex flex-column gap-2">
                                                <li><i class="bi bi-check-circle-fill text-success me-2" aria-hidden="true"></i>Salinan lesen perniagaan</li>
                                                <li><i class="bi bi-circle text-muted me-2" aria-hidden="true"></i>Pelan lokasi premis</li>
                                                <li><i class="bi bi-circle text-muted me-2" aria-hidden="true"></i>Perjanjian pengurusan sisa</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acc3" aria-expanded="false" aria-controls="acc3">
                                            <span class="flex-grow-1">Sejarah Semakan</span>
                                            <span class="badge-status badge-status-neutral me-2">3 Rekod</span>
                                        </button>
                                    </h2>
                                    <div id="acc3" class="accordion-collapse collapse">
                                        <div class="accordion-body small">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="d-flex justify-content-between"><span>Permohonan diterima</span><span class="text-muted">10 Nov 2026</span></div>
                                                <div class="d-flex justify-content-between"><span>Semakan dokumen</span><span class="text-muted">12 Nov 2026</span></div>
                                                <div class="d-flex justify-content-between"><span>Menunggu kelulusan</span><span class="text-muted">13 Nov 2026</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

                <!-- 10. Komponen: Timeline -->
                <section class="row g-3 g-lg-4 mt-0" aria-label="Timeline">
                    <div class="col-12 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4">
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-4">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Garis Masa Permohonan</h3>
                                    <p class="text-muted small mb-0">Sejarah aktiviti bagi permohonan <strong>ISISA-2026-0891</strong></p>
                                </div>
                                <span class="badge-status badge-status-warning"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Menunggu Kelulusan</span>
                            </div>

                            <!-- PHP: Gelung foreach untuk memaparkan sejarah aktiviti dari pangkalan data (gunakan htmlspecialchars()) -->
                            <ol class="timeline">
                                <li class="timeline-item">
                                    <span class="timeline-dot timeline-dot-success"><i class="bi bi-send-check" aria-hidden="true"></i></span>
                                    <div class="timeline-content">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                            <h4 class="timeline-title">Permohonan Dihantar</h4>
                                            <time class="timeline-time" datetime="2026-11-10T09:15">10 Nov 2026, 9:15 pagi</time>
                                        </div>
                                        <p class="timeline-desc">Permohonan lesen premis dihantar oleh pemohon melalui portal ISISA.</p>
                                        <span class="timeline-meta"><i class="bi bi-person" aria-hidden="true"></i>Ahmad Faiz</span>
                                    </div>
                                </li>

                                <li class="timeline-item">
                                    <span class="timeline-dot timeline-dot-info"><i class="bi bi-clipboard-check" aria-hidden="true"></i></span>
                                    <div class="timeline-content">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                            <h4 class="timeline-title">Disemak oleh Penyemak</h4>
                                            <time class="timeline-time" datetime="2026-11-11T14:40">11 Nov 2026, 2:40 petang</time>
                                        </div>
                                        <p class="timeline-desc">Semakan awal dokumen dan maklumat premis telah dilakukan.</p>
                                        <span class="timeline-meta"><i class="bi bi-person" aria-hidden="true"></i>Nurul Aina · Jabatan Pelesenan</span>
                                    </div>
                                </li>

                                <li class="timeline-item">
                                    <span class="timeline-dot timeline-dot-warning"><i class="bi bi-exclamation-lg" aria-hidden="true"></i></span>
                                    <div class="timeline-content">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                            <h4 class="timeline-title">Dokumen Tambahan Diminta</h4>
                                            <time class="timeline-time" datetime="2026-11-11T15:05">11 Nov 2026, 3:05 petang</time>
                                        </div>
                                        <p class="timeline-desc">Pemohon diminta mengemukakan pelan lokasi premis yang terkini.</p>
                                        <span class="timeline-meta"><i class="bi bi-person" aria-hidden="true"></i>Nurul Aina · Jabatan Pelesenan</span>
                                    </div>
                                </li>

                                <li class="timeline-item">
                                    <span class="timeline-dot timeline-dot-neutral"><i class="bi bi-file-earmark-arrow-up" aria-hidden="true"></i></span>
                                    <div class="timeline-content">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                            <h4 class="timeline-title">Dokumen Dikemas Kini</h4>
                                            <time class="timeline-time" datetime="2026-11-12T10:20">12 Nov 2026, 10:20 pagi</time>
                                        </div>
                                        <p class="timeline-desc">Pelan lokasi premis telah dimuat naik dan disahkan lengkap.</p>
                                        <span class="timeline-meta"><i class="bi bi-paperclip" aria-hidden="true"></i>pelan-lokasi.pdf</span>
                                    </div>
                                </li>

                                <li class="timeline-item timeline-item-current">
                                    <span class="timeline-dot timeline-dot-current"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
                                    <div class="timeline-content">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                            <h4 class="timeline-title">Menunggu Kelulusan</h4>
                                            <time class="timeline-time" datetime="2026-11-13T08:30">13 Nov 2026, 8:30 pagi</time>
                                        </div>
                                        <p class="timeline-desc">Permohonan dihantar kepada pelulus untuk keputusan akhir.</p>
                                        <span class="timeline-meta"><i class="bi bi-person" aria-hidden="true"></i>Pelulus: Dato' Rahman</span>
                                    </div>
                                </li>

                                <li class="timeline-item timeline-item-pending">
                                    <span class="timeline-dot timeline-dot-pending"><i class="bi bi-check2-all" aria-hidden="true"></i></span>
                                    <div class="timeline-content">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
                                            <h4 class="timeline-title">Keputusan Akhir</h4>
                                            <span class="timeline-time">Belum selesai</span>
                                        </div>
                                        <p class="timeline-desc mb-0">Notifikasi keputusan akan dihantar kepada pemohon selepas kelulusan.</p>
                                    </div>
                                </li>
                            </ol>
                        </div>
                    </div>
                </section>

                <!-- 11. Komponen: Mesej Notifikasi -->
                <section class="row g-3 g-lg-4 mt-0" aria-label="Mesej Notifikasi">
                    <div class="col-12 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4">
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1 d-flex align-items-center gap-2">
                                        Notifikasi
                                        <span class="notif-count" id="notifUnreadCount" aria-live="polite">5</span>
                                    </h3>
                                    <p class="text-muted small mb-0">Mesej terkini berkaitan permohonan dan sistem</p>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <div class="btn-group" role="group" aria-label="Tapis notifikasi">
                                        <button type="button" class="btn toolbar-ctl btn-primary rounded-end-0" id="notifFilterAll" aria-pressed="true">Semua</button>
                                        <button type="button" class="btn toolbar-ctl toolbar-ctl-outline rounded-start-0" id="notifFilterUnread" aria-pressed="false">Belum Dibaca</button>
                                    </div>
                                    <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" id="notifMarkAll">
                                        <i class="bi bi-check2-all" aria-hidden="true"></i><span>Tandakan Semua Dibaca</span>
                                    </button>
                                </div>
                            </div>

                            <!-- PHP: Gelung foreach untuk memaparkan notifikasi dari pangkalan data (gunakan htmlspecialchars()) -->
                            <ul class="notif-list" id="notifList">
                                <li class="notif-item unread" tabindex="0">
                                    <span class="notif-icon notif-icon-success"><i class="bi bi-check-circle" aria-hidden="true"></i></span>
                                    <div class="notif-body">
                                        <div class="notif-title">Permohonan Diluluskan</div>
                                        <div class="notif-text">Permohonan ISISA-2026-0889 telah diluluskan oleh Jabatan Pelesenan.</div>
                                    </div>
                                    <div class="notif-side"><span class="notif-time">5 minit lalu</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                                </li>

                                <li class="notif-item unread" tabindex="0">
                                    <span class="notif-icon notif-icon-warning"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span>
                                    <div class="notif-body">
                                        <div class="notif-title">Tindakan Diperlukan</div>
                                        <div class="notif-text">Permohonan ISISA-2026-0888 memerlukan dokumen tambahan sebelum 20 November 2026.</div>
                                    </div>
                                    <div class="notif-side"><span class="notif-time">32 minit lalu</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                                </li>

                                <li class="notif-item unread" tabindex="0">
                                    <span class="notif-icon notif-icon-info"><i class="bi bi-person-check" aria-hidden="true"></i></span>
                                    <div class="notif-body">
                                        <div class="notif-title">Dihantar Kepada Penyemak</div>
                                        <div class="notif-text">Permohonan ISISA-2026-0890 telah dihantar kepada Nurul Aina untuk semakan.</div>
                                    </div>
                                    <div class="notif-side"><span class="notif-time">1 jam lalu</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                                </li>

                                <li class="notif-item unread" tabindex="0">
                                    <span class="notif-icon notif-icon-danger"><i class="bi bi-x-circle" aria-hidden="true"></i></span>
                                    <div class="notif-body">
                                        <div class="notif-title">Permohonan Ditolak</div>
                                        <div class="notif-text">Permohonan ISISA-2026-0885 tidak memenuhi syarat pelesenan. Sila semak catatan penolakan.</div>
                                    </div>
                                    <div class="notif-side"><span class="notif-time">3 jam lalu</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                                </li>

                                <li class="notif-item unread" tabindex="0">
                                    <span class="notif-icon notif-icon-neutral"><i class="bi bi-gear" aria-hidden="true"></i></span>
                                    <div class="notif-body">
                                        <div class="notif-title">Penyelenggaraan Sistem</div>
                                        <div class="notif-text">Sistem tidak dapat dicapai pada 20 November 2026, 12:00–2:00 pagi.</div>
                                    </div>
                                    <div class="notif-side"><span class="notif-time">Semalam</span><span class="notif-dot" aria-label="Belum dibaca"></span></div>
                                </li>

                                <li class="notif-item" tabindex="0">
                                    <span class="notif-icon notif-icon-success"><i class="bi bi-file-earmark-check" aria-hidden="true"></i></span>
                                    <div class="notif-body">
                                        <div class="notif-title">Laporan Bulanan Tersedia</div>
                                        <div class="notif-text">Laporan pengurusan sisa bulan Oktober 2026 sedia untuk dimuat turun.</div>
                                    </div>
                                    <div class="notif-side"><span class="notif-time">2 hari lalu</span></div>
                                </li>

                                <li class="notif-item" tabindex="0">
                                    <span class="notif-icon notif-icon-info"><i class="bi bi-megaphone" aria-hidden="true"></i></span>
                                    <div class="notif-body">
                                        <div class="notif-title">Pengumuman Baharu</div>
                                        <div class="notif-text">Program Pengkomposan Sisa Organik Komuniti 2026 kini dibuka untuk permohonan.</div>
                                    </div>
                                    <div class="notif-side"><span class="notif-time">4 hari lalu</span></div>
                                </li>
                            </ul>

                            <div class="notif-empty text-center text-muted py-4 d-none" id="notifEmpty">
                                <i class="bi bi-bell-slash fs-3 d-block mb-2" aria-hidden="true"></i>
                                Tiada notifikasi belum dibaca.
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

    <!-- Butang Kembali ke Atas -->
    <button type="button" class="back-to-top" id="backToTop" aria-label="Kembali ke atas" title="Kembali ke atas">
        <i class="bi bi-arrow-up" aria-hidden="true"></i>
    </button>

    <!-- Libraries: jQuery, Bootstrap bundle, Axios, Chart.js, Select2, SweetAlert2, Toastr (see public/library/js-library.php) -->
    <?= jsLibrary('dashboard') ?>
    <!-- Page scripts: type="module" because main.js imports its modules. Must come AFTER the libraries. -->
    <script type="module" src="<?= e(asset('assets/js/dashboard-js/main.js', true)) ?>"></script>


</body>

</html>