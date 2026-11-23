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
    <title>Charts Template</title>

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

                <section class="row g-3 g-lg-4">

                    <!-- 4. Kad Carta (Chart.js) -->
                    <div class="col-12 col-lg-8 order-2 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Statistik Permohonan Bulanan</h3>
                                    <p class="text-muted small mb-0">Perbandingan permohonan diterima dan selesai (2026)</p>
                                </div>
                                <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                                    6 Bulan Terkini
                                </span>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 270px;">
                                <canvas id="isisaMonthlyChart" aria-label="Carta Statistik Permohonan Bulanan" role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 4b. Kad Carta Pai (Status Permohonan) -->
                    <div class="col-12 col-lg-4 order-3 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Status Permohonan</h3>
                                <p class="text-muted small mb-0">Pecahan mengikut status semasa</p>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 270px;">
                                <canvas id="isisaStatusPie" aria-label="Carta Pai Status Permohonan" role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 4c. Kad Carta Donut (Permohonan Mengikut Jabatan) -->
                    <div class="col-12 col-lg-4 order-4 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Permohonan Mengikut Jabatan</h3>
                                <p class="text-muted small mb-0">Agihan jumlah permohonan bulan ini</p>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 270px;">
                                <canvas id="isisaDeptDonut" aria-label="Carta Donut Permohonan Mengikut Jabatan" role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 4d. Kad Carta Garisan (Trend Mingguan) -->
                    <div class="col-12 col-lg-8 order-5 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Trend Permohonan Mingguan</h3>
                                    <p class="text-muted small mb-0">Jumlah permohonan diterima setiap minggu</p>
                                </div>
                                <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                                    8 Minggu Terkini
                                </span>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 270px;">
                                <canvas id="isisaWeeklyLine" aria-label="Carta Garisan Trend Permohonan Mingguan" role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 4e. Kad Carta Bar Mendatar -->
                    <div class="col-12 col-lg-4 order-6 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Jenis Permohonan Teratas</h3>
                                <p class="text-muted small mb-0">Lima kategori dengan permohonan terbanyak</p>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 270px;">
                                <canvas id="isisaHorizontalBar" aria-label="Carta Bar Mendatar Jenis Permohonan" role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 4f. Kad Carta Bar Bertindan -->
                    <div class="col-12 col-lg-8 order-7 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Status Permohonan Mengikut Bulan</h3>
                                    <p class="text-muted small mb-0">Pecahan status bagi setiap bulan (bar bertindan)</p>
                                </div>
                                <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                                    6 Bulan Terkini
                                </span>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 270px;">
                                <canvas id="isisaStackedBar" aria-label="Carta Bar Bertindan Status Permohonan Mengikut Bulan" role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 4g. Kad Pautan Pantas -->
                    <!-- 4g-1. Kad Carta Polar Area (di bawah Jenis Permohonan Teratas) -->
                    <div class="col-12 col-lg-4 order-8 animate-in delay-5">
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

                    <!-- 4g-2. Kad Carta Radar -->
                    <div class="col-12 col-lg-4 order-9 animate-in delay-5">
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

                    <!-- 4g-3. Kad Carta Garisan Berperingkat (Stepped Line) -->
                    <div class="col-12 col-lg-4 order-10 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Kapasiti Pegawai Bertugas</h3>
                                <p class="text-muted small mb-0">Bilangan pegawai berbanding sasaran (stepped line)</p>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 300px;">
                                <canvas id="isisaSteppedLine" aria-label="Carta Garisan Berperingkat Kapasiti Pegawai Bertugas" role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 4h. Kad Carta Garisan Bertindan -->
                    <div class="col-12 order-11 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Kutipan Sisa Mengikut Kategori</h3>
                                    <p class="text-muted small mb-0">Tonase mingguan (garisan bertindan, tan)</p>
                                </div>
                                <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                                    8 Minggu Terkini
                                </span>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 270px;">
                                <canvas id="isisaStackedLine" aria-label="Carta Garisan Bertindan Kutipan Sisa Mengikut Kategori" role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 4i. Kad Carta Combo Bar + Garisan (di bawah Kutipan Sisa Mengikut Kategori) -->
                    <div class="col-12 order-12 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Permohonan &amp; Kadar Kelulusan</h3>
                                    <p class="text-muted small mb-0">Bilangan permohonan (bar) dan peratus kelulusan (garisan)</p>
                                </div>
                                <span class="badge rounded-pill" style="background-color: var(--isisa-primary-soft); color: var(--isisa-primary); font-weight: 600;">
                                    12 Bulan
                                </span>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 300px;">
                                <canvas id="isisaComboChart" aria-label="Carta Combo Bar dan Garisan Permohonan dan Kadar Kelulusan" role="img"></canvas>
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