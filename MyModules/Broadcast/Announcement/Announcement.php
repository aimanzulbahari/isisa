<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 3) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see AnnouncementController.php): POST saves and redirects, GET returns the data to show
require_once __DIR__ . '/AnnouncementController.php';
$view  = handleAnnouncement();
$stats = $view['stats'];
$pg    = $view['page'];

// Status badge colour (see components/badges.css): ACTIVE = green, anything else = grey
$activeId = config('app.status')['ACTIVE'];
$badgeKey = static fn (int $statusId): string => $statusId === $activeId ? 'success' : 'neutral';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Pengumuman</title>

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
                                <div class="stat-label">Jumlah Pengumuman</div>
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
                                <div class="stat-label">Pengumuman Tidak Aktif</div>
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
                                <div class="stat-label">Pengumuman Aktif</div>
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
                                <div class="stat-value tabular-nums mb-1"><?= e(formatDate($stats['last_update'])) ?></div>
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
                                    <h3 class="h6 fw-semibold mb-1">Senarai Pengumuman</h3>
                                    <p class="text-muted small mb-0">Senarai rekod Pengumuman yang aktif</p>
                                </div>

                                <!-- Bar Alat: Carian, Tapis, Set Semula & Tambah Data (GET: dikendalikan di pelayan) -->
                                <form method="get" action="<?= e(routeUrl('announcement')) ?>" class="table-toolbar" role="search">
                                    <div class="toolbar-search">
                                        <i class="bi bi-search" aria-hidden="true"></i>
                                        <input type="search" id="announcementSearch" name="q" value="<?= e($view['q']) ?>" maxlength="100" class="form-control toolbar-ctl" placeholder="Cari tajuk, keterangan atau jenis..." aria-label="Carian rekod">
                                    </div>
                                    <select id="announcementFilterStatus" name="status" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut status">
                                        <option value="">Semua Status</option>
                                        <?php foreach ($view['statuses'] as $st): ?>
                                            <option value="<?= e($st['ref_status_id']) ?>" <?= (int) $st['ref_status_id'] === $view['status'] ? 'selected' : '' ?>><?= e($st['ref_status_description']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary toolbar-ctl">
                                        <i class="bi bi-funnel" aria-hidden="true"></i><span>Tapis</span>
                                    </button>
                                    <!-- Butang Set Semula: hanya dipaparkan apabila carian / tapisan sedang aktif -->
                                    <?php if ($view['q'] !== '' || $view['status'] > 0): ?>
                                        <a href="<?= e(routeUrl('announcement')) ?>" class="btn toolbar-ctl toolbar-ctl-outline">
                                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-primary toolbar-ctl" data-bs-toggle="modal" data-bs-target="#announcementModal">
                                        <i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah Pengumuman</span>
                                    </button>
                                </form>
                            </div>

                            <!-- PHP: Gelung foreach untuk memaparkan baris jadual dari pangkalan data (gunakan htmlspecialchars()) -->
                            <div class="table-responsive">
                                <table class="table table-isisa align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 60px;">No.</th>
                                            <th scope="col">Pengumuman</th>
                                            <th scope="col">Jenis</th>
                                            <th scope="col">Tempoh</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center" style="width: 110px;">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tabular-nums">
                                        <?php if ($view['items'] === []): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Tiada rekod pengumuman ditemui.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($view['items'] as $n => $row): ?>
                                            <?php $key = $badgeKey((int) $row['announcement_status_id']); ?>
                                            <tr>
                                                <td class="text-muted"><?= e($pg['offset'] + $n + 1) ?></td>
                                                <td class="fw-semibold"><?= e($row['announcement_title']) ?></td>
                                                <td><?= e($row['type_description']) ?></td>
                                                <td class="text-muted">
                                                    <?= e(formatDate($row['announcement_start_date'])) ?>
                                                    &ndash;
                                                    <?= $row['announcement_end_date'] !== null ? e(formatDate($row['announcement_end_date'])) : 'Tiada tarikh tamat' ?>
                                                </td>
                                                <td>
                                                    <span class="badge-status badge-status-<?= e($key) ?>">
                                                        <i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i><?= e($row['status_description']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-1">
                                                        <!-- The edit form is filled from these data-* values by announcement-form.js -->
                                                        <a href="#" class="btn-action btn-edit-announcement" aria-label="Sunting rekod" title="Sunting"
                                                            data-id="<?= e($row['announcement_id']) ?>"
                                                            data-tajuk="<?= e($row['announcement_title']) ?>"
                                                            data-jenis="<?= e($row['announcement_type_id']) ?>"
                                                            data-keterangan="<?= e($row['announcement_description']) ?>"
                                                            data-tarikh-mula="<?= e(substr((string) $row['announcement_start_date'], 0, 10)) ?>"
                                                            data-tarikh-tamat="<?= e(substr((string) $row['announcement_end_date'], 0, 10)) ?>"
                                                            data-status="<?= e($row['announcement_status_id']) ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
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
                                        <?php foreach ($pg['pages'] as $p): ?>
                                            <li class="page-item<?= $p === $pg['current'] ? ' active' : '' ?>">
                                                <a class="page-link" href="<?= e(pageUrl($p)) ?>" aria-label="Halaman <?= e($p) ?>"<?= $p === $pg['current'] ? ' aria-current="page"' : '' ?>><?= e($p) ?></a>
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
    <!-- Modal Tambah / Sunting Pengumuman (one form for both: announcement-form.js fills it when a row is edited) -->
    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <form class="modal-content needs-validation" id="announcementForm" method="post" action="<?= e(routeUrl('announcement')) ?>" novalidate style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <!-- CSRF token: required, or the server rejects the form. announcement_id is empty for a new announcement. -->
                <?= csrfField() ?>
                <input type="hidden" name="announcement_id" value="">
                <!-- Kekalkan carian / tapisan / halaman selepas simpan -->
                <input type="hidden" name="q" value="<?= e($view['q']) ?>">
                <input type="hidden" name="status" value="<?= e($view['status'] ?: '') ?>">
                <input type="hidden" name="page" value="<?= e($pg['current']) ?>">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="announcementModalLabel">Tambah Pengumuman</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fieldTajuk" class="form-label fw-medium small">Tajuk Pengumuman</label>
                        <input type="text" class="form-control" id="fieldTajuk" name="tajuk" placeholder="cth: Penyelenggaraan sistem berjadual" maxlength="<?= ANNOUNCEMENT_TITLE_MAX ?>" required>
                        <div class="invalid-feedback">Sila masukkan tajuk pengumuman.</div>
                    </div>
                    <div class="mb-3">
                        <label for="fieldJenis" class="form-label fw-medium small">Jenis Pengumuman</label>
                        <select class="form-select" id="fieldJenis" name="jenis" required>
                            <option value="" selected disabled>Pilih jenis pengumuman</option>
                            <?php foreach ($view['types'] as $type): ?>
                                <option value="<?= e($type['ref_announcement_type_id']) ?>"><?= e($type['ref_announcement_type_description']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Sila pilih jenis pengumuman.</div>
                    </div>
                    <div class="mb-3">
                        <label for="fieldKeterangan" class="form-label fw-medium small">Keterangan Pengumuman</label>
                        <textarea class="form-control" id="fieldKeterangan" name="keterangan" rows="4" placeholder="Tulis keterangan pengumuman di sini..." maxlength="<?= ANNOUNCEMENT_DESCRIPTION_MAX ?>" required></textarea>
                        <div class="invalid-feedback">Sila masukkan keterangan pengumuman.</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="fieldTarikhMula" class="form-label fw-medium small">Tarikh Mula</label>
                            <input type="date" class="form-control" id="fieldTarikhMula" name="tarikh_mula" required>
                            <div class="invalid-feedback">Sila pilih tarikh mula.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="fieldTarikhTamat" class="form-label fw-medium small">Tarikh Tamat</label>
                            <input type="date" class="form-control" id="fieldTarikhTamat" name="tarikh_tamat" aria-describedby="tamatHelp">
                            <div id="tamatHelp" class="form-text">Kosongkan jika tiada tarikh tamat.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="fieldStatus" class="form-label fw-medium small">Status</label>
                        <select class="form-select" id="fieldStatus" name="status_id" required>
                            <?php foreach ($view['statuses'] as $st): ?>
                                <option value="<?= e($st['ref_status_id']) ?>"><?= e($st['ref_status_description']) ?></option>
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

    <?php if ($view['old'] !== null): ?>
        <!-- The last save was refused: announcement-form.js opens the form again with what the user typed -->
        <div id="announcementOld" data-old="<?= e(json_encode($view['old'], JSON_UNESCAPED_UNICODE)) ?>" hidden></div>
    <?php endif; ?>

    <!-- Butang Kembali ke Atas -->
    <button type="button" class="back-to-top" id="backToTop" aria-label="Kembali ke atas" title="Kembali ke atas">
        <i class="bi bi-arrow-up" aria-hidden="true"></i>
    </button>

    <!-- Libraries: jQuery, Bootstrap bundle, Axios, Chart.js, Select2, SweetAlert2, Toastr (see public/library/js-library.php) -->
    <?= jsLibrary('dashboard') ?>
    <!-- Page scripts: type="module" because main.js imports its modules. Must come AFTER the libraries. -->
    <script type="module" src="<?= e(asset('assets/js/dashboard-js/main.js', true)) ?>"></script>

    <!-- Messages from the server (shown once) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof toastr !== 'undefined') {
                <?php foreach ($view['flash'] as $f): ?>
                toastr[<?= json_encode(in_array($f['type'], ['success', 'error', 'warning', 'info'], true) ? $f['type'] : 'info') ?>](<?= json_encode((string) $f['message'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>);
                <?php endforeach; ?>
            }
        });
    </script>


</body>

</html>