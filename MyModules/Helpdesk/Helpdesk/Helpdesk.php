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
    <title>Meja Bantuan</title>

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
                                    Dashboard Meja Bantuan
                                </h2>
                                <p class="mb-0 text-white text-opacity-75" style="font-size: 0.925rem;">
                                    Berikut ringkasan Meja Bantuan.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="row g-3 g-lg-4 mb-4" aria-label="Pautan Pantas dan Senarai Aduan">
                    <!-- Kad Pautan Pantas -->
                    <div class="col-12 col-xl-4 order-xl-2 animate-in delay-2">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Pautan Pantas</h3>
                                <p class="text-muted small mb-0">Capaian pantas ke modul yang kerap digunakan</p>
                            </div>
                            <!-- PHP: Gantikan href dengan laluan modul sebenar -->
                            <nav class="d-flex flex-column gap-2" aria-label="Pautan Pantas">
                                <a href="<?= e(routeUrl('meja-list')) ?>" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-card-list" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Senarai Aduan</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-plus-circle" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Aduan Baharu</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Aduan Dalam Tindakan</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Laporan Aduan</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="quick-link">
                                    <span class="quick-link-icon"><i class="bi bi-question-circle" aria-hidden="true"></i></span>
                                    <span class="quick-link-text">Bantuan &amp; Panduan</span>
                                    <i class="bi bi-chevron-right quick-link-arrow" aria-hidden="true"></i>
                                </a>
                            </nav>
                        </div>
                    </div>

                    <!-- Kad Jadual Aduan Terkini -->
                    <div class="col-12 col-xl-8 order-xl-1 animate-in delay-3">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="table-head mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Aduan Terkini</h3>
                                    <p class="text-muted small mb-0">Senarai rekod aduan terkini</p>
                                </div>

                                <!-- Bar Alat: Carian, Tapis & Set Semula -->
                                <div class="table-toolbar">
                                    <div class="toolbar-search">
                                        <i class="bi bi-search" aria-hidden="true"></i>
                                        <input type="search" id="tableSearch" class="form-control toolbar-ctl" placeholder="Cari rekod..." aria-label="Carian rekod">
                                    </div>
                                    <select id="filterStatus" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut status">
                                        <option value="">Semua Status</option>
                                        <!-- refer ref_status -->
                                    </select>
                                    <button type="button" id="btnFilter" class="btn btn-primary toolbar-ctl">
                                        <i class="bi bi-funnel" aria-hidden="true"></i><span>Tapis</span>
                                    </button>
                                    <button type="button" id="btnReset" class="btn toolbar-ctl toolbar-ctl-outline">
                                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                                    </button>
                                </div>
                            </div>

                            <!-- PHP: Gelung foreach untuk memaparkan baris jadual dari pangkalan data (gunakan htmlspecialchars()) -->
                            <div class="table-responsive">
                                <table class="table table-isisa align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 60px;">No.</th>
                                            <th scope="col">No. Rujukan</th>
                                            <th scope="col">Jabatan</th>
                                            <th scope="col">Tarikh</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center" style="width: 110px;">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tabular-nums">
                                        <tr>
                                            <td class="text-muted">1</td>
                                            <td class="fw-semibold">ISISA-2026-0891</td>
                                            <td>Jabatan Kejuruteraan</td>
                                            <td class="text-muted">13 Nov 2026</td>
                                            <td>
                                                <span class="badge-status badge-status-success">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Diluluskan
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                                                    <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">2</td>
                                            <td class="fw-semibold">ISISA-2026-0890</td>
                                            <td>Jabatan Perancangan</td>
                                            <td class="text-muted">13 Nov 2026</td>
                                            <td>
                                                <span class="badge-status badge-status-warning">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Semakan
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                                                    <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">3</td>
                                            <td class="fw-semibold">ISISA-2026-0889</td>
                                            <td>Jabatan Pelesenan</td>
                                            <td class="text-muted">12 Nov 2026</td>
                                            <td>
                                                <span class="badge-status badge-status-success">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Diluluskan
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                                                    <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">4</td>
                                            <td class="fw-semibold">ISISA-2026-0888</td>
                                            <td>Jabatan Landskap</td>
                                            <td class="text-muted">12 Nov 2026</td>
                                            <td>
                                                <span class="badge-status badge-status-danger">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Perlu Tindakan
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                                                    <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">5</td>
                                            <td class="fw-semibold">ISISA-2026-0887</td>
                                            <td>Jabatan Khidmat Pengurusan</td>
                                            <td class="text-muted">11 Nov 2026</td>
                                            <td>
                                                <span class="badge-status badge-status-success">
                                                    <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i>Diluluskan
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                                                    <a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination (10 rekod setiap halaman, see src/components/pagination.php) -->
                            <?php require SRC_PATH . '/components/pagination.php'; ?>
                        </div>
                    </div>
                </section>

                <!-- Carta -->
                <section class="row g-3 g-lg-4" aria-label="Carta Meja Bantuan">
                    <!-- Kad Carta Pai (Status Permohonan) -->
                    <div class="col-12 col-lg-4 animate-in delay-4">
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

                    <!-- Kad Carta Bar Mendatar (Jenis Permohonan Teratas) -->
                    <div class="col-12 col-lg-4 animate-in delay-5">
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

                    <!-- Kad Carta Polar Area (Aduan Mengikut Zon) -->
                    <div class="col-12 col-lg-4 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Aduan Mengikut Zon</h3>
                                <p class="text-muted small mb-0">Bilangan aduan awam bagi setiap zon (polar area)</p>
                            </div>
                            <div class="position-relative flex-grow-1" style="min-height: 270px;">
                                <canvas id="isisaPolarArea" aria-label="Carta Polar Area Aduan Mengikut Zon" role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Kad Carta Combo Bar + Garisan (Permohonan & Kadar Kelulusan) -->
                    <div class="col-12 animate-in delay-5">
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