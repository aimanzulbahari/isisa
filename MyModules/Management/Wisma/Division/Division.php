<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 4) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see DivisionController.php): POST saves and redirects, GET returns the data to show
require_once __DIR__ . '/DivisionController.php';
$view = handleDivision();

$stats = $view['stats'];
$pg    = $view['page'];
$old   = $view['old'];
$statusActive = (int) config('app.status')['ACTIVE'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Bahagian</title>
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
                                    <i class="bi bi-building" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e($stats['total']) ?></div>
                                <div class="stat-label">Jumlah Bahagian</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad 2 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-2">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-slash-circle" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e($stats['not_active']) ?></div>
                                <div class="stat-label">Bahagian Tidak Aktif</div>
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
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e($stats['active']) ?></div>
                                <div class="stat-label">Bahagian Aktif</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad 4 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-4">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-clock-history" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= $stats['last_updated'] ? e(date('d M Y', strtotime((string) $stats['last_updated']))) : '-' ?></div>
                                <div class="stat-label">Kemaskini Terakhir</div>
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
                                    <h3 class="h6 fw-semibold mb-1">Senarai Bahagian</h3>
                                    <p class="text-muted small mb-0">Senarai rekod bahagian</p>
                                </div>

                                <!-- Bar Alat: Carian, Tapis, Set Semula & Tambah Data (GET: dikendalikan di pelayan) -->
                                <form method="get" action="<?= e(routeUrl('wisma-division')) ?>" class="table-toolbar" role="search">
                                    <div class="toolbar-search">
                                        <i class="bi bi-search" aria-hidden="true"></i>
                                        <input type="search" id="divSearch" name="q" value="<?= e($view['q']) ?>" maxlength="100" class="form-control toolbar-ctl" placeholder="Cari kod atau nama..." aria-label="Carian rekod">
                                    </div>
                                    <select id="divFilterStatus" name="status" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut status">
                                        <option value="">Semua Status</option>
                                        <?php foreach ($view['statusOptions'] as $opt): ?>
                                            <option value="<?= e($opt['ref_status_id']) ?>" <?= (int) $opt['ref_status_id'] === $view['status'] ? 'selected' : '' ?>><?= e($opt['ref_status_description']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary toolbar-ctl">
                                        <i class="bi bi-funnel" aria-hidden="true"></i><span>Tapis</span>
                                    </button>
                                    <!-- Butang Set Semula: hanya dipaparkan apabila carian / tapisan sedang aktif -->
                                    <?php if ($view['q'] !== '' || $view['status'] > 0): ?>
                                        <a href="<?= e(routeUrl('wisma-division')) ?>" class="btn toolbar-ctl toolbar-ctl-outline">
                                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-primary toolbar-ctl" id="btnAddDivision" data-bs-toggle="modal" data-bs-target="#divisionModal">
                                        <i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah Bahagian</span>
                                    </button>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-isisa align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 60px;">No.</th>
                                            <th scope="col">Kod Bahagian</th>
                                            <th scope="col">Nama Bahagian</th>
                                            <th scope="col">Kemaskini</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center" style="width: 110px;">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tabular-nums">
                                        <?php if ($view['rows'] === []): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Tiada rekod bahagian ditemui.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($view['rows'] as $i => $row): ?>
                                            <tr>
                                                <td class="text-muted"><?= e($pg['offset'] + $i + 1) ?></td>
                                                <td class="fw-semibold"><?= e($row['ref_division_code']) ?></td>
                                                <td><?= e($row['ref_division_description']) ?></td>
                                                <td class="text-muted"><?= e(date('d M Y', strtotime((string) $row['updated_at']))) ?></td>
                                                <td><?= statusBadge((int) $row['ref_status_id'], $row['ref_status_description']) ?></td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-1">
                                                        <button type="button" class="btn-action btn-edit-division" aria-label="Sunting rekod" title="Sunting"
                                                            data-bs-toggle="modal" data-bs-target="#divisionModal"
                                                            data-id="<?= e($row['ref_division_id']) ?>"
                                                            data-code="<?= e($row['ref_division_code']) ?>"
                                                            data-description="<?= e($row['ref_division_description']) ?>"
                                                            data-status="<?= e($row['ref_status_id']) ?>">
                                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                                        </button>
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
    <!-- Modal Tambah / Sunting Bahagian (POST ke pelayan; mod ditetapkan oleh action + id) -->
    <div class="modal fade" id="divisionModal" tabindex="-1" aria-labelledby="divisionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content needs-validation" id="divisionForm" method="post" action="<?= e(routeUrl('wisma-division')) ?>" novalidate style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <?= csrfField() ?>
                <input type="hidden" name="action" id="divAction" value="create">
                <input type="hidden" name="id" id="divId" value="">
                <!-- Kekalkan carian / tapisan / halaman selepas simpan -->
                <input type="hidden" name="q" value="<?= e($view['q']) ?>">
                <input type="hidden" name="status" value="<?= e($view['status'] ?: '') ?>">
                <input type="hidden" name="page" value="<?= e($pg['current']) ?>">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="divisionModalLabel">Tambah Bahagian</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fieldKod" class="form-label fw-medium small">Kod Bahagian</label>
                        <input type="text" class="form-control text-uppercase" id="fieldKod" name="code" placeholder="cth: JBG" maxlength="<?= e(DIVISION_CODE_MAX) ?>" required>
                        <div class="invalid-feedback">Sila masukkan kod bahagian.</div>
                    </div>
                    <div class="mb-3">
                        <label for="fieldNama" class="form-label fw-medium small">Nama Bahagian</label>
                        <input type="text" class="form-control" id="fieldNama" name="description" placeholder="cth: Bahagian Audit Dalam" maxlength="<?= e(DIVISION_NAME_MAX) ?>" required>
                        <div class="invalid-feedback">Sila masukkan nama bahagian.</div>
                    </div>
                    <div class="mb-3">
                        <label for="fieldStatus" class="form-label fw-medium small">Status</label>
                        <select class="form-select" id="fieldStatus" name="status_id" required>
                            <?php foreach ($view['statusOptions'] as $opt): ?>
                                <option value="<?= e($opt['ref_status_id']) ?>" <?= (int) $opt['ref_status_id'] === $statusActive ? 'selected' : '' ?>><?= e($opt['ref_status_description']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Sila pilih status.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
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

    <!-- Halaman Bahagian: isi borang sunting, buka semula borang jika simpan ditolak, papar mesej (toastr) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('divisionModal');
            var form = document.getElementById('divisionForm');
            var defaultStatus = <?= json_encode((string) $statusActive) ?>;

            function fillForm(mode, data) {
                document.getElementById('divAction').value = mode;
                document.getElementById('divId').value = data.id || '';
                document.getElementById('fieldKod').value = data.code || '';
                document.getElementById('fieldNama').value = data.description || '';
                document.getElementById('fieldStatus').value = data.status || defaultStatus;
                document.getElementById('divisionModalLabel').textContent = mode === 'update' ? 'Sunting Bahagian' : 'Tambah Bahagian';
                form.classList.remove('was-validated');
            }

            // Butang Tambah / Sunting (modal dibuka oleh data-bs-toggle)
            document.getElementById('btnAddDivision').addEventListener('click', function () {
                fillForm('create', {});
            });
            document.querySelectorAll('.btn-edit-division').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    fillForm('update', {
                        id: btn.dataset.id, code: btn.dataset.code,
                        description: btn.dataset.description, status: btn.dataset.status
                    });
                });
            });

            // Semakan ringkas sebelum hantar (pelayan tetap mengesahkan semula)
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.classList.add('was-validated');
                }
            });

            <?php if ($old !== null): ?>
            // Simpan ditolak: buka semula borang dengan nilai yang ditaip
            fillForm(<?= json_encode((string) $old['action']) ?>, {
                id: <?= json_encode((string) $old['id']) ?>,
                code: <?= json_encode((string) $old['code']) ?>,
                description: <?= json_encode((string) $old['description']) ?>,
                status: <?= json_encode((string) $old['status']) ?>
            });
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            <?php endif; ?>

            // Mesej (papar sekali sahaja)
            if (typeof toastr !== 'undefined') {
                <?php foreach ($view['flash'] as $f): ?>
                toastr[<?= json_encode(in_array($f['type'], ['success', 'error', 'warning', 'info'], true) ? $f['type'] : 'info') ?>](<?= json_encode((string) $f['message'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>);
                <?php endforeach; ?>
            }
        });
    </script>


</body>

</html>
