<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 3) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see ListController.php): POST saves and redirects, GET returns the data to show
require_once __DIR__ . '/ListController.php';
$view  = handleHelpdeskList();
$stats = $view['stats'];
$pg    = $view['page'];
$old   = $view['old'];

// Badge colour for each severity code
$severityClass = ['LOW' => 'secondary', 'MEDIUM' => 'info text-dark', 'HIGH' => 'warning text-dark', 'CRITICAL' => 'danger'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Senarai Aduan</title>

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
                <section class="row g-3 g-lg-4 mb-4" aria-label="Ringkasan Statistik">
                    <?php foreach ([
                        ['icon' => 'bi-ticket-detailed', 'value' => $stats['total'],       'label' => 'Jumlah Aduan'],
                        ['icon' => 'bi-envelope-open',   'value' => $stats['open'],        'label' => 'Aduan Dibuka'],
                        ['icon' => 'bi-arrow-repeat',    'value' => $stats['in_progress'], 'label' => 'Aduan Dalam Tindakan'],
                        ['icon' => 'bi-check2-circle',   'value' => $stats['closed'],      'label' => 'Aduan Ditutup'],
                    ] as $i => $card): ?>
                        <div class="col-12 col-sm-6 col-xl-3 animate-in delay-<?= e($i + 1) ?>">
                            <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="stat-icon-circle">
                                        <i class="bi <?= e($card['icon']) ?>" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="stat-value tabular-nums mb-1"><?= e($card['value']) ?></div>
                                    <div class="stat-label"><?= e($card['label']) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>

                <!-- 4. Jadual Senarai Aduan -->
                <section class="row g-3 g-lg-4">

                    <div class="col-12 order-1 animate-in delay-4">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="table-head mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Senarai Aduan</h3>
                                    <p class="text-muted small mb-0"><?= $view['showCreator'] ? 'Senarai semua aduan' : 'Senarai aduan anda' ?></p>
                                </div>

                                <!-- Bar Alat: Carian, Tapis, Set Semula & Tambah Aduan (GET: dikendalikan di pelayan) -->
                                <form method="get" action="<?= e(routeUrl('meja-list')) ?>" class="table-toolbar" role="search">
                                    <div class="toolbar-search">
                                        <i class="bi bi-search" aria-hidden="true"></i>
                                        <input type="search" id="ticketSearch" name="q" value="<?= e($view['q']) ?>" maxlength="100" class="form-control toolbar-ctl" placeholder="Cari no. rujukan atau tajuk..." aria-label="Carian rekod">
                                    </div>
                                    <select id="ticketFilterStatus" name="status" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut status">
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
                                        <a href="<?= e(routeUrl('meja-list')) ?>" class="btn toolbar-ctl toolbar-ctl-outline">
                                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" id="btnAddTicket" class="btn btn-primary toolbar-ctl" data-bs-toggle="modal" data-bs-target="#addDataModal">
                                        <i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah Aduan</span>
                                    </button>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-isisa align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 60px;">No.</th>
                                            <th scope="col">No. Rujukan</th>
                                            <th scope="col">Tajuk</th>
                                            <th scope="col">Jenis</th>
                                            <th scope="col">Keterukan</th>
                                            <?php if ($view['showCreator']): ?><th scope="col">Pengadu</th><?php endif; ?>
                                            <th scope="col">Tarikh</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center" style="width: 110px;">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tabular-nums">
                                        <?php if ($view['rows'] === []): ?>
                                            <tr>
                                                <td colspan="<?= $view['showCreator'] ? 9 : 8 ?>" class="text-center text-muted py-4">Tiada rekod aduan ditemui.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($view['rows'] as $i => $row): ?>
                                            <tr>
                                                <td class="text-muted"><?= e($pg['offset'] + $i + 1) ?></td>
                                                <td class="fw-semibold"><?= e($row['helpdesk_reference_no']) ?></td>
                                                <td><?= e(strLimit((string) $row['helpdesk_title'], 50)) ?></td>
                                                <td><?= e($row['type_description']) ?></td>
                                                <td><span class="badge bg-<?= e($severityClass[$row['severity_code']] ?? 'light text-dark') ?>"><?= e($row['severity_description']) ?></span></td>
                                                <?php if ($view['showCreator']): ?><td><?= e($row['created_by_name'] ?? '-') ?></td><?php endif; ?>
                                                <td class="text-muted"><?= e(date('d M Y', strtotime((string) $row['created_at']))) ?></td>
                                                <td><?= statusBadge((int) $row['status_id'], $row['status_description']) ?></td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-1">
                                                        <button type="button" class="btn-action btn-view-ticket" aria-label="Lihat rekod" title="Lihat"
                                                            data-bs-toggle="modal" data-bs-target="#viewDataModal"
                                                            data-ref="<?= e($row['helpdesk_reference_no']) ?>"
                                                            data-title="<?= e($row['helpdesk_title']) ?>"
                                                            data-type="<?= e($row['type_description']) ?>"
                                                            data-severity="<?= e($row['severity_description']) ?>"
                                                            data-status="<?= e($row['status_description']) ?>"
                                                            data-creator="<?= e($row['created_by_name'] ?? '-') ?>"
                                                            data-date="<?= e(date('d M Y, H:i', strtotime((string) $row['created_at']))) ?>"
                                                            data-description="<?= e($row['helpdesk_description']) ?>"
                                                            data-attachment="<?= $row['helpdesk_attachment'] ? e(routeUrl('meja-attachment', ['id' => $row['helpdesk_id']])) : '' ?>">
                                                            <i class="bi bi-eye" aria-hidden="true"></i>
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
                                <?php if ($pg['last'] > 1): // Nombor halaman hanya dipaparkan jika ada lebih daripada satu halaman ?>
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
                                <?php endif; ?>
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

    <!-- Modal Tambah Aduan (POST + fail lampiran ke pelayan; No. Rujukan dijana semasa simpan) -->
    <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <form class="modal-content form-isisa needs-validation" id="addDataForm" method="post" action="<?= e(routeUrl('meja-list')) ?>" enctype="multipart/form-data" novalidate style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="create">
                <!-- Kekalkan carian / tapisan / halaman selepas simpan -->
                <input type="hidden" name="q" value="<?= e($view['q']) ?>">
                <input type="hidden" name="status" value="<?= e($view['status'] ?: '') ?>">
                <input type="hidden" name="page" value="<?= e($pg['current']) ?>">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="addDataModalLabel">Tambah Aduan</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fieldRujukan" class="form-label fw-medium small">No. Rujukan</label>
                        <input type="text" class="form-control" id="fieldRujukan" value="Dijana automatik semasa aduan dihantar" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="fieldJenisAduan" class="form-label fw-medium small">Jenis Aduan</label>
                        <select class="form-select" id="fieldJenisAduan" name="type_id" required>
                            <option value="" selected>Pilih Jenis Aduan</option>
                            <?php foreach ($view['typeOptions'] as $opt): ?>
                                <option value="<?= e($opt['ref_helpdesk_type_id']) ?>"><?= e($opt['ref_helpdesk_type_description']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Sila pilih jenis aduan.</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-label fw-medium small" id="severityLabel">Tahap Keterukan</div>
                        <!-- Radio kotak khas (custom-controls.css): menegak pada skrin kecil -->
                        <div class="isisa-choice-row" style="--cols: <?= e(max(1, count($view['severityOptions']))) ?>" role="radiogroup" aria-labelledby="severityLabel">
                            <?php foreach ($view['severityOptions'] as $n => $opt): ?>
                                <label class="isisa-radio is-tile" for="sev<?= e($opt['ref_severity_id']) ?>">
                                    <input type="radio" name="severity_id" id="sev<?= e($opt['ref_severity_id']) ?>" value="<?= e($opt['ref_severity_id']) ?>" <?= $n === 0 ? 'checked' : '' ?>>
                                    <span class="isisa-radio-dot" aria-hidden="true"></span>
                                    <span class="isisa-check-label"><?= e($opt['ref_severity_description']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fieldTajuk" class="form-label fw-medium small">Tajuk Aduan</label>
                        <input type="text" class="form-control" id="fieldTajuk" name="title" placeholder="cth: Sistem tidak dapat dilayari" maxlength="<?= e(HELPDESK_TITLE_MAX) ?>" required>
                        <div class="invalid-feedback">Sila masukkan tajuk aduan.</div>
                    </div>

                    <div class="col-12 col-md-12 mb-3">
                        <label for="inTextarea" class="form-label">Keterangan Aduan</label>
                        <textarea class="form-control" id="inTextarea" name="description" rows="4" placeholder="Tulis keterangan aduan anda di sini..." maxlength="<?= e(HELPDESK_DESC_MAX) ?>" required></textarea>
                        <div class="invalid-feedback">Sila masukkan keterangan aduan.</div>
                    </div>

                    <div class="col-12 col-md-12">
                        <label for="inFile" class="form-label">Muat Naik Fail (pilihan)</label>
                        <input type="file" class="form-control" id="inFile" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">PDF, JPG atau PNG. Maksimum 5 MB.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Hantar Aduan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Lihat Aduan (diisi oleh JavaScript daripada data-* pada butang Lihat) -->
    <div class="modal fade" id="viewDataModal" tabindex="-1" aria-labelledby="viewDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="viewDataModalLabel">Butiran Aduan</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted fw-medium small">No. Rujukan</dt><dd class="col-sm-8" id="viewRef"></dd>
                        <dt class="col-sm-4 text-muted fw-medium small">Tajuk</dt><dd class="col-sm-8" id="viewTitle"></dd>
                        <dt class="col-sm-4 text-muted fw-medium small">Jenis Aduan</dt><dd class="col-sm-8" id="viewType"></dd>
                        <dt class="col-sm-4 text-muted fw-medium small">Tahap Keterukan</dt><dd class="col-sm-8" id="viewSeverity"></dd>
                        <dt class="col-sm-4 text-muted fw-medium small">Status</dt><dd class="col-sm-8" id="viewStatus"></dd>
                        <dt class="col-sm-4 text-muted fw-medium small">Pengadu</dt><dd class="col-sm-8" id="viewCreator"></dd>
                        <dt class="col-sm-4 text-muted fw-medium small">Tarikh</dt><dd class="col-sm-8" id="viewDate"></dd>
                        <dt class="col-sm-4 text-muted fw-medium small">Keterangan</dt><dd class="col-sm-8" id="viewDescription" style="white-space: pre-line;"></dd>
                        <dt class="col-sm-4 text-muted fw-medium small">Lampiran</dt>
                        <dd class="col-sm-8" id="viewAttachmentWrap">
                            <a href="#" id="viewAttachment" target="_blank" rel="noopener"><i class="bi bi-paperclip" aria-hidden="true"></i> Buka lampiran</a>
                            <span id="viewNoAttachment" class="text-muted">Tiada lampiran</span>
                        </dd>
                    </dl>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Libraries: jQuery, Bootstrap bundle, Axios, Chart.js, Select2, SweetAlert2, Toastr (see public/library/js-library.php) -->
    <?= jsLibrary('dashboard') ?>
    <!-- Page scripts: type="module" because main.js imports its modules. Must come AFTER the libraries. -->
    <script type="module" src="<?= e(asset('assets/js/dashboard-js/main.js', true)) ?>"></script>

    <!-- Halaman Senarai Aduan: borang Tambah, modal Lihat, buka semula borang jika simpan ditolak, papar mesej (toastr) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var addModalEl = document.getElementById('addDataModal');
            var form = document.getElementById('addDataForm');

            document.getElementById('btnAddTicket').addEventListener('click', function () {
                form.reset();
                form.classList.remove('was-validated');
            });

            // Semakan ringkas sebelum hantar (pelayan tetap mengesahkan semula)
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.classList.add('was-validated');
                }
            });

            // Modal Lihat: isi butiran daripada data-* pada butang
            document.querySelectorAll('.btn-view-ticket').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var d = btn.dataset;
                    document.getElementById('viewRef').textContent = d.ref;
                    document.getElementById('viewTitle').textContent = d.title;
                    document.getElementById('viewType').textContent = d.type;
                    document.getElementById('viewSeverity').textContent = d.severity;
                    document.getElementById('viewStatus').textContent = d.status;
                    document.getElementById('viewCreator').textContent = d.creator;
                    document.getElementById('viewDate').textContent = d.date;
                    document.getElementById('viewDescription').textContent = d.description;
                    var link = document.getElementById('viewAttachment');
                    link.style.display = d.attachment ? '' : 'none';
                    link.href = d.attachment || '#';
                    document.getElementById('viewNoAttachment').style.display = d.attachment ? 'none' : '';
                });
            });

            <?php if ($old !== null): ?>
            // Simpan ditolak: buka semula borang dengan nilai yang ditaip (fail lampiran perlu dipilih semula)
            document.getElementById('fieldJenisAduan').value = <?= json_encode((string) ($old['type_id'] ?: '')) ?>;
            var sev = document.getElementById('sev' + <?= json_encode((string) $old['severity_id']) ?>);
            if (sev) { sev.checked = true; }
            document.getElementById('fieldTajuk').value = <?= json_encode((string) $old['title']) ?>;
            document.getElementById('inTextarea').value = <?= json_encode((string) $old['description']) ?>;
            bootstrap.Modal.getOrCreateInstance(addModalEl).show();
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