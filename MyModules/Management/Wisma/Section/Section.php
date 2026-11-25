<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 4) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see SectionController.php): POST saves and redirects, GET returns the data to show
require_once __DIR__ . '/SectionController.php';
$view = handleSection();

$stats = $view['stats'];
$old   = $view['old'];
$statusActive = (int) config('app.status')['ACTIVE'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Seksyen</title>

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
                                <div class="stat-label">Jumlah Seksyen</div>
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
                                <div class="stat-label">Seksyen Tidak Aktif</div>
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
                                <div class="stat-label">Seksyen Aktif</div>
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
                                    <h3 class="h6 fw-semibold mb-1">Senarai Seksyen</h3>
                                    <p class="text-muted small mb-0">Senarai rekod Seksyen yang aktif</p>
                                </div>

                                <!-- Bar Alat: Carian, Tapis, Set Semula & Tambah Data (GET: dikendalikan di pelayan) -->
                                <form method="get" action="<?= e(routeUrl('wisma-section')) ?>" class="table-toolbar" role="search">
                                    <div class="toolbar-search">
                                        <i class="bi bi-search" aria-hidden="true"></i>
                                        <input type="search" id="sectionSearch" name="q" value="<?= e($view['q']) ?>" maxlength="100" class="form-control toolbar-ctl" placeholder="Cari kod, nama, keterangan atau jabatan..." aria-label="Carian rekod">
                                    </div>
                                    <select id="sectionFilterStatus" name="status" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut status">
                                        <option value="">Semua Status</option>
                                        <?php foreach ($view['statusOptions'] as $opt): ?>
                                            <option value="<?= e($opt['ref_status_id']) ?>" <?= (int) $opt['ref_status_id'] === $view['status'] ? 'selected' : '' ?>><?= e($opt['ref_status_description']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary toolbar-ctl">
                                        <i class="bi bi-funnel" aria-hidden="true"></i><span>Tapis</span>
                                    </button>
                                    <!-- Butang Set Semula: hanya dipaparkan apabila carian / tapisan sedang aktif -->
                                    <?php if ($view['filtering']): ?>
                                        <a href="<?= e(routeUrl('wisma-section')) ?>" class="btn toolbar-ctl toolbar-ctl-outline">
                                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-primary toolbar-ctl" id="btnAddSection" data-bs-toggle="modal" data-bs-target="#sectionModal">
                                        <i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah Seksyen</span>
                                    </button>
                                </form>
                            </div>

                            <?php if ($view['groups'] === []): ?>
                                <div class="text-center text-muted py-4">Tiada rekod seksyen ditemui.</div>
                            <?php endif; ?>

                            <!-- Satu akordion bagi setiap jabatan. Semua kumpulan dibuka semasa carian / tapisan aktif,
                                 atau kumpulan jabatan yang baru disimpan (?open=ID jabatan). -->
                            <div class="accordion accordion-isisa" id="sectionAccordion">
                                <?php foreach ($view['groups'] as $group): ?>
                                    <?php
                                    $isOpen     = $view['filtering'] || $group['id'] === $view['open'];
                                    $collapseId = 'dept' . $group['id'];
                                    ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button<?= $isOpen ? '' : ' collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= e($collapseId) ?>" aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" aria-controls="<?= e($collapseId) ?>">
                                                <i class="bi bi-building acc-icon" aria-hidden="true"></i>
                                                <span class="flex-grow-1"><?= e($group['name']) ?></span>
                                                <span class="section-count"><?= e(count($group['sections'])) ?> Seksyen</span>
                                            </button>
                                        </h2>
                                        <div id="<?= e($collapseId) ?>" class="accordion-collapse collapse<?= $isOpen ? ' show' : '' ?>"<?= $view['filtering'] ? '' : ' data-bs-parent="#sectionAccordion"' ?>>
                                            <div class="accordion-body p-0">
                                                <ul class="section-list list-unstyled mb-0">
                                                    <?php foreach ($group['sections'] as $i => $row): ?>
                                                        <li class="section-row">
                                                            <span class="section-no text-muted"><?= e($i + 1) ?></span>
                                                            <div class="section-info">
                                                                <div class="section-name"><?= e($row['ref_section_name']) ?> <span class="text-muted small">(<?= e($row['ref_section_code']) ?>)</span></div>
                                                                <?php if (trim((string) $row['ref_section_description']) !== ''): ?>
                                                                    <div class="section-desc"><?= e($row['ref_section_description']) ?></div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?= statusBadge((int) $row['ref_status_id'], $row['ref_status_description']) ?>
                                                            <div class="d-inline-flex gap-1">
                                                                <button type="button" class="btn-action btn-edit-section" aria-label="Sunting seksyen" title="Sunting"
                                                                    data-bs-toggle="modal" data-bs-target="#sectionModal"
                                                                    data-id="<?= e($row['ref_section_id']) ?>"
                                                                    data-department="<?= e($row['ref_department_id']) ?>"
                                                                    data-code="<?= e($row['ref_section_code']) ?>"
                                                                    data-name="<?= e($row['ref_section_name']) ?>"
                                                                    data-description="<?= e($row['ref_section_description']) ?>"
                                                                    data-status="<?= e($row['ref_status_id']) ?>">
                                                                    <i class="bi bi-pencil" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="small text-muted mt-3 pt-3 border-top" aria-live="polite">
                                Menunjukkan <?= e($view['total']) ?> seksyen dalam <?= e(count($view['groups'])) ?> jabatan
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
    <!-- Modal Tambah / Sunting Seksyen (POST ke pelayan; mod ditetapkan oleh action + id) -->
    <div class="modal fade" id="sectionModal" tabindex="-1" aria-labelledby="sectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content needs-validation" id="sectionForm" method="post" action="<?= e(routeUrl('wisma-section')) ?>" novalidate style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <?= csrfField() ?>
                <input type="hidden" name="action" id="sectionAction" value="create">
                <input type="hidden" name="id" id="sectionId" value="">
                <!-- Kekalkan carian / tapisan selepas simpan -->
                <input type="hidden" name="q" value="<?= e($view['q']) ?>">
                <input type="hidden" name="status" value="<?= e($view['status'] ?: '') ?>">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="sectionModalLabel">Tambah Seksyen</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fieldJabatan" class="form-label fw-medium small">Jabatan</label>
                        <select class="form-select" id="fieldJabatan" name="department_id" required>
                            <option value="">Pilih Jabatan</option>
                            <?php foreach ($view['departments'] as $dept): ?>
                                <option value="<?= e($dept['ref_department_id']) ?>"><?= e($dept['ref_department_description']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Sila pilih jabatan.</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="fieldKodSeksyen" class="form-label fw-medium small">Kod Seksyen</label>
                            <input type="text" class="form-control text-uppercase" id="fieldKodSeksyen" name="code" placeholder="cth: APL" maxlength="<?= e(SECTION_CODE_MAX) ?>" required>
                            <div class="invalid-feedback">Sila masukkan kod seksyen.</div>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="fieldNamaSeksyen" class="form-label fw-medium small">Nama Seksyen</label>
                            <input type="text" class="form-control" id="fieldNamaSeksyen" name="name" placeholder="cth: Seksyen Aplikasi" maxlength="<?= e(SECTION_NAME_MAX) ?>" required>
                            <div class="invalid-feedback">Sila masukkan nama seksyen.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="fieldKeterangan" class="form-label fw-medium small">Keterangan Seksyen</label>
                        <textarea class="form-control" id="fieldKeterangan" name="description" rows="4" maxlength="<?= e(SECTION_DESC_MAX) ?>" placeholder="Tulis keterangan seksyen di sini..."></textarea>
                        <div class="form-text">Pilihan. Maksimum <?= e(SECTION_DESC_MAX) ?> aksara.</div>
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

    <!-- Halaman Seksyen: isi borang sunting, buka semula borang jika simpan ditolak, papar mesej (toastr) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('sectionModal');
            var form = document.getElementById('sectionForm');
            var defaultStatus = <?= json_encode((string) $statusActive) ?>;

            function fillForm(mode, data) {
                document.getElementById('sectionAction').value = mode;
                document.getElementById('sectionId').value = data.id || '';
                document.getElementById('fieldJabatan').value = data.department || '';
                document.getElementById('fieldKodSeksyen').value = data.code || '';
                document.getElementById('fieldNamaSeksyen').value = data.name || '';
                document.getElementById('fieldKeterangan').value = data.description || '';
                document.getElementById('fieldStatus').value = data.status || defaultStatus;
                document.getElementById('sectionModalLabel').textContent = mode === 'update' ? 'Sunting Seksyen' : 'Tambah Seksyen';
                form.classList.remove('was-validated');
            }

            // Butang Tambah / Sunting (modal dibuka oleh data-bs-toggle)
            document.getElementById('btnAddSection').addEventListener('click', function () {
                fillForm('create', {});
            });
            document.querySelectorAll('.btn-edit-section').forEach(function (btn) {
                btn.addEventListener('click', function () { fillForm('update', btn.dataset); });
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
                department: <?= json_encode((string) $old['department_id']) ?>,
                code: <?= json_encode((string) $old['code']) ?>,
                name: <?= json_encode((string) $old['name']) ?>,
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