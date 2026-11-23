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
    <title>Toastr Template</title>

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

                <section class="row g-3 g-lg-4" aria-label="Senarai Button dan Toastr">

                    <!-- Senarai Button -->
                    <div class="col-12 col-lg-7 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Senarai Button</h3>
                                <p class="text-muted small mb-0">Variasi butang yang konsisten dengan tema ISISA</p>
                            </div>

                            <div class="demo-label">Varian</div>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <button type="button" class="btn btn-primary toolbar-ctl">Utama</button>
                                <button type="button" class="btn toolbar-ctl btn-soft">Lembut</button>
                                <button type="button" class="btn toolbar-ctl toolbar-ctl-outline">Garis</button>
                                <button type="button" class="btn toolbar-ctl btn-danger-soft">Bahaya</button>
                                <button type="button" class="btn toolbar-ctl btn-link-isisa">Pautan</button>
                            </div>

                            <div class="demo-label">Dengan ikon</div>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <button type="button" class="btn btn-primary toolbar-ctl"><i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah</span></button>
                                <button type="button" class="btn toolbar-ctl btn-soft"><i class="bi bi-download" aria-hidden="true"></i><span>Muat Turun</span></button>
                                <button type="button" class="btn toolbar-ctl toolbar-ctl-outline"><i class="bi bi-printer" aria-hidden="true"></i><span>Cetak</span></button>
                                <button type="button" class="btn toolbar-ctl btn-danger-soft"><i class="bi bi-trash" aria-hidden="true"></i><span>Padam</span></button>
                                <button type="button" class="btn toolbar-ctl btn-export btn-export-pdf"><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i><span>PDF</span></button>
                                <button type="button" class="btn toolbar-ctl btn-export btn-export-excel"><i class="bi bi-file-earmark-excel" aria-hidden="true"></i><span>Excel</span></button>
                                <button type="button" class="btn toolbar-ctl btn-export btn-export-csv"><i class="bi bi-filetype-csv" aria-hidden="true"></i><span>CSV</span></button>
                            </div>

                            <div class="demo-label">Ikon sahaja &amp; kumpulan</div>
                            <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                                <div class="d-inline-flex gap-1">
                                    <a href="#" class="btn-action" aria-label="Lihat" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a>
                                    <a href="#" class="btn-action" aria-label="Sunting" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                    <a href="#" class="btn-action" aria-label="Padam" title="Padam"><i class="bi bi-trash" aria-hidden="true"></i></a>
                                </div>
                                <div class="btn-group" role="group" aria-label="Kumpulan butang">
                                    <button type="button" class="btn toolbar-ctl toolbar-ctl-outline rounded-end-0">Hari</button>
                                    <button type="button" class="btn toolbar-ctl btn-primary rounded-0">Minggu</button>
                                    <button type="button" class="btn toolbar-ctl toolbar-ctl-outline rounded-start-0">Bulan</button>
                                </div>
                            </div>

                            <div class="demo-label">Tindakan permohonan</div>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <button type="button" class="btn btn-primary toolbar-ctl" data-flow="hantar"><i class="bi bi-send" aria-hidden="true"></i><span>Hantar</span></button>
                                <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" data-flow="setsemula"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span></button>
                                <button type="button" class="btn btn-primary toolbar-ctl" data-flow="terima"><i class="bi bi-check2-square" aria-hidden="true"></i><span>Terima Permohonan</span></button>
                                <button type="button" class="btn toolbar-ctl btn-danger-solid" data-flow="tolak"><i class="bi bi-x-square" aria-hidden="true"></i><span>Tolak Permohonan</span></button>
                                <button type="button" class="btn btn-primary toolbar-ctl" data-flow="lulus"><i class="bi bi-check-circle" aria-hidden="true"></i><span>Lulus</span></button>
                                <button type="button" class="btn toolbar-ctl btn-danger-solid" data-flow="gagal"><i class="bi bi-x-circle" aria-hidden="true"></i><span>Gagal</span></button>
                                <button type="button" class="btn toolbar-ctl btn-soft" data-flow="penyemak"><i class="bi bi-person-check" aria-hidden="true"></i><span>Hantar Kepada Penyemak</span></button>
                                <button type="button" class="btn toolbar-ctl btn-soft" data-flow="pelulus"><i class="bi bi-person-check-fill" aria-hidden="true"></i><span>Hantar Kepada Pelulus</span></button>
                            </div>

                            <div class="demo-label">Dilumpuhkan</div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-primary toolbar-ctl" disabled>Utama</button>
                                <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" disabled>Garis</button>
                                <button type="button" class="btn toolbar-ctl btn-soft" disabled>Lembut</button>
                            </div>
                        </div>
                    </div>

                    <!-- Toastr -->
                    <div class="col-12 col-lg-5 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Toastr</h3>
                                <p class="text-muted small mb-0">Notifikasi sementara yang muncul di penjuru skrin</p>
                            </div>

                            <div class="demo-label">Jenis notifikasi</div>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <button type="button" class="btn btn-primary toolbar-ctl" data-toast="success"><i class="bi bi-check-circle" aria-hidden="true"></i><span>Berjaya</span></button>
                                <button type="button" class="btn toolbar-ctl btn-soft" data-toast="info"><i class="bi bi-info-circle" aria-hidden="true"></i><span>Makluman</span></button>
                                <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" data-toast="warning"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i><span>Amaran</span></button>
                                <button type="button" class="btn toolbar-ctl btn-danger-soft" data-toast="error"><i class="bi bi-x-circle" aria-hidden="true"></i><span>Ralat</span></button>
                            </div>

                            <div class="demo-label">Mesej tersuai (pilihan)</div>
                            <div class="row g-2 mb-4 form-isisa">
                                <div class="col-12 col-sm-5">
                                    <input type="text" class="form-control" id="toastTitle" placeholder="Tajuk" aria-label="Tajuk toastr">
                                </div>
                                <div class="col-12 col-sm-7">
                                    <input type="text" class="form-control" id="toastText" placeholder="Mesej" aria-label="Mesej toastr">
                                </div>
                            </div>

                            <div class="demo-label">Tetapan</div>
                            <div class="row g-3 mb-4 form-isisa">
                                <div class="col-12 col-sm-6">
                                    <label for="toastPosition" class="form-label small mb-1">Kedudukan</label>
                                    <select class="form-select" id="toastPosition">
                                        <option value="toast-top-right" selected>Atas kanan</option>
                                        <option value="toast-top-left">Atas kiri</option>
                                        <option value="toast-top-center">Atas tengah</option>
                                        <option value="toast-bottom-right">Bawah kanan</option>
                                        <option value="toast-bottom-left">Bawah kiri</option>
                                        <option value="toast-bottom-center">Bawah tengah</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="toastDuration" class="form-label small mb-1">Tempoh paparan</label>
                                    <select class="form-select" id="toastDuration">
                                        <option value="2000">2 saat</option>
                                        <option value="4000" selected>4 saat</option>
                                        <option value="8000">8 saat</option>
                                        <option value="0">Kekal (tutup manual)</option>
                                    </select>
                                </div>
                                <div class="col-12 d-flex flex-wrap gap-x-4 column-gap-4 row-gap-1">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="toastProgress" checked>
                                        <label class="form-check-label small" for="toastProgress">Bar progres</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="toastClose" checked>
                                        <label class="form-check-label small" for="toastClose">Butang tutup</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="toastNewest" checked>
                                        <label class="form-check-label small" for="toastNewest">Terbaharu di atas</label>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn toolbar-ctl toolbar-ctl-outline" id="btnClearToasts">
                                <i class="bi bi-x-lg" aria-hidden="true"></i><span>Kosongkan Semua</span>
                            </button>
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