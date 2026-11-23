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
    <title>Alerts Template</title>

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

                <section class="row g-3 g-lg-4" aria-label="Status Badge dan Alerts">

                    <!-- Status Badge -->
                    <div class="col-12 col-lg-6 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Status Badge</h3>
                                <p class="text-muted small mb-0">Penanda status untuk jadual, senarai dan kad</p>
                            </div>

                            <div class="demo-label">Lembut (dengan titik)</div>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <span class="badge-status badge-status-success"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Diluluskan</span>
                                <span class="badge-status badge-status-warning"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Semakan</span>
                                <span class="badge-status badge-status-danger"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Perlu Tindakan</span>
                                <span class="badge-status badge-status-info"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Dalam Proses</span>
                                <span class="badge-status badge-status-neutral"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Draf</span>
                            </div>

                            <div class="demo-label">Dengan ikon</div>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <span class="badge-status badge-status-success"><i class="bi bi-check-circle" aria-hidden="true"></i>Selesai</span>
                                <span class="badge-status badge-status-warning"><i class="bi bi-hourglass-split" aria-hidden="true"></i>Menunggu</span>
                                <span class="badge-status badge-status-danger"><i class="bi bi-x-circle" aria-hidden="true"></i>Ditolak</span>
                                <span class="badge-status badge-status-info"><i class="bi bi-info-circle" aria-hidden="true"></i>Makluman</span>
                                <span class="badge-status badge-status-neutral"><i class="bi bi-dash-circle" aria-hidden="true"></i>Dibatalkan</span>
                            </div>

                            <div class="demo-label">Pepejal</div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge-status badge-solid-success">Aktif</span>
                                <span class="badge-status badge-solid-warning">Tertunggak</span>
                                <span class="badge-status badge-solid-danger">Kritikal</span>
                                <span class="badge-status badge-solid-info">Baharu</span>
                                <span class="badge-status badge-solid-neutral">Tidak Aktif</span>
                            </div>

                            <div class="demo-label mt-4">Meja Bantuan</div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge-status badge-severity-low">Rendah</span>
                                <span class="badge-status badge-severity-medium">Sederhana</span>
                                <span class="badge-status badge-severity-high">Tinggi</span>
                                <span class="badge-status badge-severity-critical">Kritikal</span>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <div class="col-12 col-lg-6 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Alerts</h3>
                                <p class="text-muted small mb-0">Mesej dalam halaman untuk makluman, amaran dan ralat</p>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                <div class="alert alert-success d-flex align-items-start gap-2 mb-0" role="alert">
                                    <i class="bi bi-check-circle-fill alert-icon" aria-hidden="true"></i>
                                    <div><strong>Berjaya!</strong> Permohonan anda telah dihantar dan sedang menunggu semakan.</div>
                                </div>

                                <div class="alert alert-info d-flex align-items-start gap-2 mb-0" role="alert">
                                    <i class="bi bi-info-circle-fill alert-icon" aria-hidden="true"></i>
                                    <div><strong>Makluman.</strong> Penyelenggaraan sistem dijadualkan pada 20 November 2026, 12:00–2:00 pagi.</div>
                                </div>

                                <div class="alert alert-warning d-flex align-items-start gap-2 mb-0" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill alert-icon" aria-hidden="true"></i>
                                    <div><strong>Amaran.</strong> Lesen premis anda akan tamat tempoh dalam 14 hari.</div>
                                </div>

                                <div class="alert alert-danger d-flex align-items-start gap-2 mb-0" role="alert">
                                    <i class="bi bi-x-circle-fill alert-icon" aria-hidden="true"></i>
                                    <div><strong>Ralat.</strong> Rekod tidak dapat disimpan. Sila semak maklumat dan cuba lagi.</div>
                                </div>

                                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-check2-circle alert-icon" aria-hidden="true"></i>
                                        <div><strong>Boleh ditutup.</strong> Klik butang silang untuk menutup alert ini.</div>
                                    </div>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Tutup"></button>
                                </div>
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