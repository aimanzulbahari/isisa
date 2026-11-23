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
    <title>Basic Template</title>

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

                <!-- 4. Jadual Aktiviti Terkini -->
                <section class="row g-3 g-lg-4">

                    <!-- 5. Kad Jadual Aktiviti Terkini -->
                    <div class="col-12 order-1 animate-in delay-4">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="table-head mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Aktiviti Terkini</h3>
                                    <p class="text-muted small mb-0">Kemas kini rekod terbaru merentas jabatan</p>
                                </div>

                                <!-- Bar Alat: Carian, Tapis, Set Semula & Tambah Data -->
                                <div class="table-toolbar">
                                    <div class="toolbar-search">
                                        <i class="bi bi-search" aria-hidden="true"></i>
                                        <input type="search" id="tableSearch" class="form-control toolbar-ctl" placeholder="Cari rekod..." aria-label="Carian rekod">
                                    </div>
                                    <select id="filterStatus" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut status">
                                        <option value="">Semua Status</option>
                                        <option value="success">Diluluskan</option>
                                        <option value="warning">Semakan</option>
                                        <option value="danger">Perlu Tindakan</option>
                                    </select>
                                    <select id="filterDept" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut jabatan">
                                        <option value="">Semua Jabatan</option>
                                        <option>Jabatan Kejuruteraan</option>
                                        <option>Jabatan Perancangan</option>
                                        <option>Jabatan Pelesenan</option>
                                        <option>Jabatan Landskap</option>
                                        <option>Jabatan Khidmat Pengurusan</option>
                                    </select>
                                    <button type="button" id="btnFilter" class="btn btn-primary toolbar-ctl">
                                        <i class="bi bi-funnel" aria-hidden="true"></i><span>Tapis</span>
                                    </button>
                                    <button type="button" id="btnReset" class="btn toolbar-ctl toolbar-ctl-outline">
                                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                                    </button>
                                    <button type="button" class="btn btn-primary toolbar-ctl" data-bs-toggle="modal" data-bs-target="#addDataModal">
                                        <i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah Data</span>
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

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content needs-validation" id="addDataForm" novalidate style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <!-- PHP: Hantar ke pelayan melalui POST bersama CSRF token & sahkan input di bahagian pelayan -->
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="addDataModalLabel">Tambah Data</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fieldRujukan" class="form-label fw-medium small">No. Rujukan</label>
                        <input type="text" class="form-control" id="fieldRujukan" placeholder="cth: ISISA-2026-0892" maxlength="30" required>
                        <div class="invalid-feedback">Sila masukkan no. rujukan.</div>
                    </div>
                    <div class="mb-3">
                        <label for="fieldJabatan" class="form-label fw-medium small">Jabatan</label>
                        <select class="form-select" id="fieldJabatan" required>
                            <option value="" selected disabled>Pilih jabatan</option>
                            <option>Jabatan Kejuruteraan</option>
                            <option>Jabatan Perancangan</option>
                            <option>Jabatan Pelesenan</option>
                            <option>Jabatan Landskap</option>
                            <option>Jabatan Khidmat Pengurusan</option>
                        </select>
                        <div class="invalid-feedback">Sila pilih jabatan.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="fieldTarikh" class="form-label fw-medium small">Tarikh</label>
                            <input type="date" class="form-control" id="fieldTarikh" required>
                            <div class="invalid-feedback">Sila pilih tarikh.</div>
                        </div>
                        <div class="col-sm-6">
                            <label for="fieldStatus" class="form-label fw-medium small">Status</label>
                            <select class="form-select" id="fieldStatus" required>
                                <option value="success">Diluluskan</option>
                                <option value="warning">Semakan</option>
                                <option value="danger">Perlu Tindakan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Libraries: jQuery, Bootstrap bundle, Axios, Chart.js, Select2, SweetAlert2, Toastr (see public/library/js-library.php) -->
    <?= jsLibrary('dashboard') ?>
    <!-- Page scripts: type="module" because main.js imports its modules. Must come AFTER the libraries. -->
    <script type="module" src="<?= e(asset('assets/js/dashboard-js/main.js', true)) ?>"></script>


</body>

</html>