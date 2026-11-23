<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 4) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see AuditController.php): returns one page of the audit log to show
require_once __DIR__ . '/AuditController.php';
$view = handleAudit();

$stats   = $view['stats'];
$pg      = $view['page'];
$filters = $view['filters'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Jejak Audit</title>

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
                <!-- 4 Kad Statistik (Grid: 4 lajur desktop, 2 lajur tablet, 1 lajur mobile) -->
                <section class="row g-3 g-lg-4 mb-4" aria-label="Ringkasan Statistik">
                    <!-- Kad 1 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-1">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-journal-text" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e(number_format($stats['total'])) ?></div>
                                <div class="stat-label">Jumlah Log</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad 2 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-2">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-calendar-day" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e(number_format($stats['today'])) ?></div>
                                <div class="stat-label">Log Hari Ini</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad 3 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-3">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-people" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e(number_format($stats['users_today'])) ?></div>
                                <div class="stat-label">Pengguna Aktif Hari Ini</div>
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
                                <div class="stat-value tabular-nums mb-1"><?= $stats['last_activity'] ? e(date('d M Y', strtotime((string) $stats['last_activity']))) : '-' ?></div>
                                <div class="stat-label">Aktiviti Terakhir</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Jadual Jejak Audit -->
                <section class="row g-3 g-lg-4">
                    <div class="col-12 order-1 animate-in delay-4">
                        <div class="isisa-card p-3 p-md-4 h-100 d-flex flex-column">
                            <div class="table-head mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">Senarai Jejak Audit</h3>
                                    <p class="text-muted small mb-0">Rekod semua tindakan dalam sistem (baca sahaja)</p>
                                </div>

                                <!-- Bar Alat: Carian & Tapis (GET: dikendalikan di pelayan) -->
                                <form method="get" action="<?= e(routeUrl('system-audit')) ?>" class="table-toolbar" role="search">
                                    <div class="toolbar-search">
                                        <i class="bi bi-search" aria-hidden="true"></i>
                                        <input type="search" id="auditSearch" name="q" value="<?= e($filters['q'] ?? '') ?>" maxlength="100" class="form-control toolbar-ctl" placeholder="Cari pengguna, tindakan, IP..." aria-label="Carian rekod">
                                    </div>
                                    <select id="auditFilterEvent" name="event" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut tindakan">
                                        <option value="">Semua Tindakan</option>
                                        <?php foreach ($view['events'] as $event): ?>
                                            <option value="<?= e($event) ?>" <?= ($filters['event'] ?? '') === $event ? 'selected' : '' ?>><?= e($event) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select id="auditFilterType" name="type" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut jadual">
                                        <option value="">Semua Jadual</option>
                                        <?php foreach ($view['types'] as $type): ?>
                                            <option value="<?= e($type) ?>" <?= ($filters['type'] ?? '') === $type ? 'selected' : '' ?>><?= e($type) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="date" id="auditFrom" name="from" value="<?= e($filters['from'] ?? '') ?>" class="form-control toolbar-ctl" aria-label="Dari tarikh" title="Dari tarikh">
                                    <input type="date" id="auditTo" name="to" value="<?= e($filters['to'] ?? '') ?>" class="form-control toolbar-ctl" aria-label="Hingga tarikh" title="Hingga tarikh">
                                    <button type="submit" class="btn btn-primary toolbar-ctl">
                                        <i class="bi bi-funnel" aria-hidden="true"></i><span>Tapis</span>
                                    </button>
                                    <!-- Butang Set Semula: hanya dipaparkan apabila carian / tapisan sedang aktif -->
                                    <?php if ($filters !== []): ?>
                                        <a href="<?= e(routeUrl('system-audit')) ?>" class="btn toolbar-ctl toolbar-ctl-outline">
                                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-isisa align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 60px;">No.</th>
                                            <th scope="col">Masa</th>
                                            <th scope="col">Pengguna</th>
                                            <th scope="col">Tindakan</th>
                                            <th scope="col">Jadual / ID</th>
                                            <th scope="col">Alamat IP</th>
                                            <th scope="col" class="text-center" style="width: 110px;">Butiran</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tabular-nums">
                                        <?php if ($view['rows'] === []): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">Tiada rekod jejak audit ditemui.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($view['rows'] as $i => $row): ?>
                                            <tr>
                                                <td class="text-muted"><?= e($pg['offset'] + $i + 1) ?></td>
                                                <td class="text-muted"><?= e(date('d M Y, H:i:s', strtotime((string) $row['created_at']))) ?></td>
                                                <td>
                                                    <?php if ($row['fullname'] !== null): ?>
                                                        <span class="fw-semibold"><?= e($row['fullname']) ?></span>
                                                        <div class="text-muted small"><?= e($row['myID']) ?></div>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge-status badge-status-<?= e(auditEventClass((string) $row['event'])) ?>">
                                                        <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i><?= e($row['event']) ?>
                                                    </span>
                                                </td>
                                                <td><?= e($row['auditable_type']) ?> <span class="text-muted">#<?= e($row['auditable_id']) ?></span></td>
                                                <td class="text-muted"><?= displayValue($row['ip_address']) ?></td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-1">
                                                        <button type="button" class="btn-action btn-view-audit" aria-label="Lihat butiran" title="Lihat"
                                                            data-bs-toggle="modal" data-bs-target="#auditModal"
                                                            data-detail="<?= e(json_encode($row['detail'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR)) ?>">
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

    <!-- Modal Butiran Audit (diisi oleh JavaScript dengan textContent, bukan HTML) -->
    <div class="modal fade" id="auditModal" tabindex="-1" aria-labelledby="auditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="auditModalLabel">Butiran Audit</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <dl class="row small mb-3" id="auditMeta"></dl>
                    <h3 class="h6 fw-semibold mb-2">Perubahan Data</h3>
                    <div class="table-responsive">
                        <table class="table table-isisa align-middle small mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Medan</th>
                                    <th scope="col">Sebelum</th>
                                    <th scope="col">Selepas</th>
                                </tr>
                            </thead>
                            <tbody id="auditChanges"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
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

    <!-- Halaman Jejak Audit: isi tetingkap butiran, papar mesej (toastr) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var meta = document.getElementById('auditMeta');
            var changes = document.getElementById('auditChanges');

            function text(value) {
                if (value === null || value === undefined) return '-';
                return typeof value === 'object' ? JSON.stringify(value) : String(value);
            }

            function fill(d) {
                meta.replaceChildren();
                [
                    ['ID Audit', d.id], ['Masa', d.time], ['Pengguna', d.user], ['Tindakan', d.event],
                    ['Jadual', d.type + ' #' + d.record], ['Alamat IP', d.ip], ['URL', d.url], ['Pelayar', d.agent]
                ].forEach(function (pair) {
                    var dt = document.createElement('dt');
                    dt.className = 'col-sm-3';
                    dt.textContent = pair[0];
                    var dd = document.createElement('dd');
                    dd.className = 'col-sm-9 text-break';
                    dd.textContent = pair[1] === '' ? '-' : text(pair[1]);
                    meta.append(dt, dd);
                });

                changes.replaceChildren();
                var oldV = Array.isArray(d.old) ? {} : (d.old || {});
                var newV = Array.isArray(d.new) ? {} : (d.new || {});
                var keys = Object.keys(oldV);
                Object.keys(newV).forEach(function (k) { if (keys.indexOf(k) === -1) keys.push(k); });

                if (keys.length === 0) {
                    var tr = document.createElement('tr');
                    var td = document.createElement('td');
                    td.colSpan = 3;
                    td.className = 'text-center text-muted';
                    td.textContent = 'Tiada data perubahan direkodkan.';
                    tr.appendChild(td);
                    changes.appendChild(tr);
                    return;
                }
                keys.forEach(function (k) {
                    var tr = document.createElement('tr');
                    [k, oldV[k], newV[k]].forEach(function (value, idx) {
                        var td = document.createElement('td');
                        td.className = idx === 0 ? 'fw-semibold' : 'text-break';
                        td.textContent = text(value);
                        tr.appendChild(td);
                    });
                    changes.appendChild(tr);
                });
            }

            document.querySelectorAll('.btn-view-audit').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    try { fill(JSON.parse(btn.dataset.detail)); } catch (err) { fill({ id: '-', old: {}, new: {}, type: '-', record: '-' }); }
                });
            });

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
