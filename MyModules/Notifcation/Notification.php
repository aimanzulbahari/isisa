<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 2) . '/core/config/bootstrap.php';

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
    <title>Notifikasi</title>

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
                <section class="welcome-banner mb-4 animate-in delay-1" aria-label="Alu-aluan Pengguna">
                    <div class="position-relative" style="z-index: 1;">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>
                                <!-- PHP: Nama pengguna di bawah ialah placeholder. Gantikan dengan sesi PHP & lindungi dengan htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8') -->
                                <h2 class="welcome-title">
                                    Notifikasi
                                </h2>
                                <p class="mb-0 text-white text-opacity-75" style="font-size: 0.925rem;">
                                    Berikut Ringkasan Notifikasi.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Notifikasi -->
                <section class="row g-3 g-lg-4" aria-label="Mesej Notifikasi">
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