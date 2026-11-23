<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 4) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see BackupDataController.php): POST/download are handled and exit, GET returns the data to show
require_once __DIR__ . '/BackupDataController.php';
$view = handleBackupData();
$pg = $view['page'];
$filters = $view['filters'];
$stats = $view['stats'];
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Backup Data</title>

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
                <section class="row g-3 g-lg-4 mb-4" aria-label="Ringkasan Statistik">
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-1">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-database" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e($stats['total']) ?></div>
                                <div class="stat-label">Jumlah Backup</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-3">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-x-circle" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e($stats['failed']) ?></div>
                                <div class="stat-label">Backup Gagal</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-2">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e($stats['success']) ?></div>
                                <div class="stat-label">Backup Berjaya</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-4">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-clock-history" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= $stats['last_backup'] !== null ? e(date('d M Y', strtotime($stats['last_backup']))) : '-' ?></div>
                                <div class="stat-label">Backup Terakhir</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="row g-3 g-lg-4">
                    <div class="col-12 order-1 animate-in delay-4">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="table-head mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Senarai Sandaran Data</h3>
                                    <p class="text-muted small mb-0">Senarai rekod sandaran data pangkalan data</p>
                                </div>

                                <!-- Bar Alat: Carian & Tapis (GET: dikendalikan di pelayan) -->
                                <form method="get" action="<?= e(routeUrl('system-backup')) ?>" class="table-toolbar" role="search">
                                    <div class="toolbar-search">
                                        <i class="bi bi-search" aria-hidden="true"></i>
                                        <input type="search" name="q" value="<?= e($filters['q'] ?? '') ?>" maxlength="100" class="form-control toolbar-ctl" placeholder="Cari kod, nama, pengguna..." aria-label="Carian rekod">
                                    </div>
                                    <select name="status" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut status">
                                        <option value="">Semua Status</option>
                                        <option value="success" <?= ($filters['status'] ?? '') === 'success' ? 'selected' : '' ?>>Berjaya</option>
                                        <option value="failed" <?= ($filters['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Gagal</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary toolbar-ctl">
                                        <i class="bi bi-funnel" aria-hidden="true"></i><span>Tapis</span>
                                    </button>
                                    <?php if ($filters !== []): ?>
                                        <a href="<?= e(routeUrl('system-backup')) ?>" class="btn toolbar-ctl toolbar-ctl-outline">
                                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-primary toolbar-ctl" data-bs-toggle="modal" data-bs-target="#backupModal">
                                        <i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah Backup</span>
                                    </button>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-isisa align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 60px;">No.</th>
                                            <th scope="col">Kod</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Saiz</th>
                                            <th scope="col">Dibuat Oleh</th>
                                            <th scope="col">Tarikh</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center" style="width: 110px;">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tabular-nums">
                                        <?php if ($view['rows'] === []): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">Tiada rekod sandaran ditemui.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($view['rows'] as $i => $row): ?>
                                            <?php $ok = (int) $row['status_id'] === config('app.status')['SUCCESS']; ?>
                                            <tr>
                                                <td class="text-muted"><?= e($pg['offset'] + $i + 1) ?></td>
                                                <td class="fw-semibold"><?= e($row['data_backup_code']) ?></td>
                                                <td><?= e($row['data_backup_name']) ?></td>
                                                <td class="text-muted"><?= $ok ? e(formatBytes((int) $row['file_size'])) : '-' ?></td>
                                                <td><?= displayValue($row['created_by_name']) ?></td>
                                                <td class="text-muted"><?= e(date('d M Y, H:i', strtotime((string) $row['created_at']))) ?></td>
                                                <td>
                                                    <span class="badge-status badge-status-<?= $ok ? 'success' : 'danger' ?>">
                                                        <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i><?= $ok ? 'Berjaya' : 'Gagal' ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-1">
                                                        <?php if ($ok): ?>
                                                            <a href="<?= e(routeUrl('system-backup', ['download' => $row['data_backup_id']])) ?>" class="btn-action" aria-label="Muat turun sandaran" title="Muat turun"><i class="bi bi-download" aria-hidden="true"></i></a>
                                                        <?php endif; ?>
                                                        <form method="post" action="<?= e(routeUrl('system-backup')) ?>" class="d-inline js-backup-delete" data-code="<?= e($row['data_backup_code']) ?>">
                                                            <?= csrfField() ?>
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="id" value="<?= e($row['data_backup_id']) ?>">
                                                            <button type="submit" class="btn-action border-0 bg-transparent" aria-label="Padam sandaran" title="Padam"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination (10 rekod setiap halaman, dikira di pelayan) -->
                            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 mt-3 pt-3 border-top">
                                <div class="small text-muted" aria-live="polite">
                                    Menunjukkan <?= e($pg['from']) ?>–<?= e($pg['to']) ?> daripada <?= e($pg['total']) ?> rekod
                                </div>
                                <nav aria-label="Navigasi halaman jadual">
                                    <ul class="pagination pagination-isisa mb-0">
                                        <li class="page-item<?= $pg['has_prev'] ? '' : ' disabled' ?>">
                                            <a class="page-link" href="<?= e(pageUrl(max(1, $pg['current'] - 1))) ?>" aria-label="Halaman sebelumnya"><i class="bi bi-chevron-left" aria-hidden="true"></i></a>
                                        </li>
                                        <?php foreach ($pg['pages'] as $n): ?>
                                            <li class="page-item<?= $n === $pg['current'] ? ' active' : '' ?>">
                                                <a class="page-link" href="<?= e(pageUrl($n)) ?>" aria-label="Halaman <?= e($n) ?>"<?= $n === $pg['current'] ? ' aria-current="page"' : '' ?>><?= e($n) ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                        <li class="page-item<?= $pg['has_next'] ? '' : ' disabled' ?>">
                                            <a class="page-link" href="<?= e(pageUrl(min($pg['last'], $pg['current'] + 1))) ?>" aria-label="Halaman seterusnya"><i class="bi bi-chevron-right" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </nav>
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


    <!-- Modal Tambah Backup -->
    <div class="modal fade" id="backupModal" tabindex="-1" aria-labelledby="backupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="<?= e(routeUrl('system-backup')) ?>" class="modal-content needs-validation" id="backupForm" novalidate style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <!-- Dihantar melalui POST bersama CSRF token; semua input disahkan semula di BackupDataController.php -->
                <?= csrfField() ?>
                <input type="hidden" name="action" value="create">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="backupModalLabel">Tambah Backup</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="fieldBackupName" class="form-label fw-medium small">Nama Sandaran</label>
                        <input type="text" class="form-control" id="fieldBackupName" name="backup_name" value="<?= e($view['old_name']) ?>" placeholder="cth: Sandaran Mingguan" maxlength="255" required>
                        <div class="invalid-feedback">Sila masukkan nama sandaran.</div>
                    </div>
                    <p class="text-muted small mb-0">Sandaran penuh pangkalan data akan disimpan dalam folder storan pelayan. Ini mungkin mengambil sedikit masa.</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Messages from the server (see BackupDataController.php)
            if (typeof toastr !== 'undefined') {
                <?php foreach ($view['flash'] as $f): ?>
                toastr[<?= json_encode(in_array($f['type'], ['success', 'error', 'warning', 'info'], true) ? $f['type'] : 'info') ?>](<?= json_encode((string) $f['message'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>);
                <?php endforeach; ?>
            }

            // Re-open the modal after a refused save so the typed name is not lost
            <?php if ($view['old_name'] !== ''): ?>
            if (window.bootstrap) bootstrap.Modal.getOrCreateInstance(document.getElementById('backupModal')).show();
            <?php endif; ?>

            // Ask before deleting a backup
            document.querySelectorAll('.js-backup-delete').forEach(function (form) {
                form.addEventListener('submit', function (ev) {
                    if (!window.confirm('Padam sandaran ' + form.dataset.code + '? Fail akan dipadam dan tidak boleh dipulihkan.')) {
                        ev.preventDefault();
                    }
                });
            });

            // Validate, and stop double clicks: a backup can take a while
            var backupForm = document.getElementById('backupForm');
            if (backupForm) {
                backupForm.addEventListener('submit', function (ev) {
                    if (!backupForm.checkValidity()) { ev.preventDefault(); backupForm.classList.add('was-validated'); return; }
                    var btn = backupForm.querySelector('button[type="submit"]');
                    if (btn) { btn.disabled = true; btn.textContent = 'Sedang membuat sandaran...'; }
                });
            }
        });
    </script>

</body>

</html>
