<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 4) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see EmployeeController.php): POST saves and redirects, GET returns the data to show
require_once __DIR__ . '/EmployeeController.php';
$view = handleEmployee();

$stats = $view['stats'];
$pg    = $view['page'];
$old   = $view['old'];
$statusActive = (int) config('app.status')['ACTIVE'];

/** <option> list for a reference dropdown (Seksyen also carries its department ID). */
$opts = static function (array $rows, string $key, string $label, bool $withDept = false): string {
    $html = '<option value="">-- Pilih --</option>';
    foreach ($rows as $r) {
        $html .= '<option value="' . e($r[$key]) . '"'
            . ($withDept ? ' data-department="' . e($r['ref_department_id']) . '"' : '') . '>'
            . e($r[$label]) . '</option>';
    }
    return $html;
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Kakitangan</title>

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
                                    <i class="bi bi-people" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e($stats['total']) ?></div>
                                <div class="stat-label">Jumlah Kakitangan</div>
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
                                <div class="stat-label">Kakitangan Tidak Aktif</div>
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
                                <div class="stat-label">Kakitangan Aktif</div>
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
                                    <h3 class="h6 fw-semibold mb-1">Senarai Kakitangan</h3>
                                    <p class="text-muted small mb-0">Senarai rekod Kakitangan yang aktif</p>
                                </div>

                                <!-- Bar Alat: Carian, Tapis, Set Semula & Tambah Data (GET: dikendalikan di pelayan) -->
                                <form method="get" action="<?= e(routeUrl('users-employee')) ?>" class="table-toolbar" role="search">
                                    <div class="toolbar-search">
                                        <i class="bi bi-search" aria-hidden="true"></i>
                                        <input type="search" id="empSearch" name="q" value="<?= e($view['q']) ?>" maxlength="100" class="form-control toolbar-ctl" placeholder="Cari nama, no. KP atau email..." aria-label="Carian rekod">
                                    </div>
                                    <select id="empFilterStatus" name="status" class="form-select toolbar-ctl toolbar-select" aria-label="Tapis mengikut status">
                                        <option value="">Semua Status</option>
                                        <?php foreach ($view['statusOptions'] as $opt): ?>
                                            <option value="<?= e($opt['ref_status_id']) ?>" <?= (int) $opt['ref_status_id'] === $view['status'] ? 'selected' : '' ?>><?= e($opt['ref_status_description']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary toolbar-ctl">
                                        <i class="bi bi-funnel" aria-hidden="true"></i><span>Tapis</span>
                                    </button>
                                    <?php if ($view['q'] !== '' || $view['status'] > 0): ?>
                                        <a href="<?= e(routeUrl('users-employee')) ?>" class="btn toolbar-ctl toolbar-ctl-outline">
                                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Set Semula</span>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-primary toolbar-ctl" id="btnAddEmployee" data-bs-toggle="modal" data-bs-target="#employeeModal">
                                        <i class="bi bi-plus-lg" aria-hidden="true"></i><span>Tambah Kakitangan</span>
                                    </button>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-isisa align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 60px;">No.</th>
                                            <th scope="col">Kakitangan</th>
                                            <th scope="col">No. KP</th>
                                            <th scope="col">Jabatan</th>
                                            <th scope="col">Peranan</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center" style="width: 110px;">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tabular-nums">
                                        <?php if ($view['rows'] === []): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">Tiada rekod kakitangan ditemui.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($view['rows'] as $i => $row): ?>
                                            <tr>
                                                <td class="text-muted"><?= e($pg['offset'] + $i + 1) ?></td>
                                                <td>
                                                    <div class="fw-semibold"><?= e($row['fullname']) ?></div>
                                                    <div class="small text-muted"><?= e($row['email']) ?></div>
                                                </td>
                                                <td><?= e(formatMyID((string) $row['myID'])) ?></td>
                                                <td><?= e($row['ref_department_description'] ?? '-') ?></td>
                                                <td><?= e($row['ref_role_description'] ?? '-') ?></td>
                                                <td><?= statusBadge((int) $row['ref_status_id'], $row['ref_status_description']) ?></td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-1">
                                                        <button type="button" class="btn-action btn-edit-employee" aria-label="Sunting rekod" title="Sunting"
                                                            data-bs-toggle="modal" data-bs-target="#employeeModal"
                                                            data-id="<?= e($row['users_id']) ?>"
                                                            data-myid="<?= e($row['myID']) ?>"
                                                            data-fullname="<?= e($row['fullname']) ?>"
                                                            data-email="<?= e($row['email']) ?>"
                                                            data-phone="<?= e($row['phone_number']) ?>"
                                                            data-ext="<?= e($row['office_extension']) ?>"
                                                            data-department="<?= e($row['ref_department_id']) ?>"
                                                            data-section="<?= e($row['ref_section_id']) ?>"
                                                            data-division="<?= e($row['ref_division_id']) ?>"
                                                            data-branch="<?= e($row['ref_branch_id']) ?>"
                                                            data-role="<?= e($row['ref_role_id']) ?>"
                                                            data-roletype="<?= e($row['ref_role_type_id']) ?>"
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
    <!-- Modal Tambah / Sunting Kakitangan (POST ke pelayan; mod ditetapkan oleh action + id) -->
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <form class="modal-content needs-validation" id="employeeForm" method="post" action="<?= e(routeUrl('users-employee')) ?>" novalidate style="border-radius: var(--isisa-radius-lg); border: 1px solid var(--isisa-border);">
                <?= csrfField() ?>
                <input type="hidden" name="action" id="empAction" value="create">
                <input type="hidden" name="id" id="empId" value="">
                <!-- Kekalkan carian / tapisan / halaman selepas simpan -->
                <input type="hidden" name="q" value="<?= e($view['q']) ?>">
                <input type="hidden" name="status" value="<?= e($view['status'] ?: '') ?>">
                <input type="hidden" name="page" value="<?= e($pg['current']) ?>">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="employeeModalLabel">Tambah Kakitangan</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label for="fieldFullname" class="form-label fw-medium small">Nama Penuh</label>
                            <input type="text" class="form-control" id="fieldFullname" name="fullname" placeholder="cth: AHMAD BIN ALI" maxlength="255" required>
                            <div class="invalid-feedback">Sila masukkan nama penuh.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="fieldMyID" class="form-label fw-medium small">No. Kad Pengenalan</label>
                            <input type="text" class="form-control" id="fieldMyID" name="myID" placeholder="cth: 900101105544" maxlength="14" required inputmode="numeric" autocomplete="off">
                            <div class="invalid-feedback">Sila masukkan no. kad pengenalan 12 digit.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label for="fieldDob" class="form-label fw-medium small">Tarikh Lahir</label>
                            <input type="text" class="form-control" id="fieldDob" placeholder="Dikira secara automatik" readonly tabindex="-1">
                        </div>
                        <div class="col-md-6">
                            <label for="fieldAge" class="form-label fw-medium small">Umur</label>
                            <input type="text" class="form-control" id="fieldAge" placeholder="Dikira secara automatik" readonly tabindex="-1">
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label for="fieldEmail" class="form-label fw-medium small">Email</label>
                            <input type="email" class="form-control" id="fieldEmail" name="email" placeholder="cth: nama@jabatan.gov.my" maxlength="150" required>
                            <div class="invalid-feedback">Sila masukkan email yang sah.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="fieldPhone" class="form-label fw-medium small">No. Telefon</label>
                            <input type="text" class="form-control" id="fieldPhone" name="phone_number" placeholder="cth: 012-345 6789" maxlength="20" required inputmode="tel">
                            <div class="invalid-feedback">Sila masukkan no. telefon yang sah.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="fieldExt" class="form-label fw-medium small">Sambungan (ext.)</label>
                            <input type="text" class="form-control" id="fieldExt" name="office_extension" placeholder="cth: 1234" maxlength="20" inputmode="numeric">
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label for="fieldDepartment" class="form-label fw-medium small">Jabatan</label>
                            <select class="form-select" id="fieldDepartment" name="ref_department_id" required>
                                <?= $opts($view['departments'], 'ref_department_id', 'ref_department_description') ?>
                            </select>
                            <div class="invalid-feedback">Sila pilih jabatan.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="fieldSection" class="form-label fw-medium small">Seksyen</label>
                            <select class="form-select" id="fieldSection" name="ref_section_id" required>
                                <?= $opts($view['sections'], 'ref_section_id', 'ref_section_name', true) ?>
                            </select>
                            <div class="invalid-feedback">Sila pilih seksyen.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label for="fieldDivision" class="form-label fw-medium small">Bahagian</label>
                            <select class="form-select" id="fieldDivision" name="ref_division_id" required>
                                <?= $opts($view['divisions'], 'ref_division_id', 'ref_division_description') ?>
                            </select>
                            <div class="invalid-feedback">Sila pilih bahagian.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="fieldBranch" class="form-label fw-medium small">Cawangan</label>
                            <select class="form-select" id="fieldBranch" name="ref_branch_id" required>
                                <?= $opts($view['branches'], 'ref_branch_id', 'ref_branch_description') ?>
                            </select>
                            <div class="invalid-feedback">Sila pilih cawangan.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label for="fieldRole" class="form-label fw-medium small">Peranan</label>
                            <select class="form-select" id="fieldRole" name="ref_role_id" required>
                                <?= $opts($view['roles'], 'ref_role_id', 'ref_role_description') ?>
                            </select>
                            <div class="invalid-feedback">Sila pilih peranan.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="fieldRoleType" class="form-label fw-medium small">Jenis Peranan</label>
                            <select class="form-select" id="fieldRoleType" name="ref_role_type_id" required>
                                <?= $opts($view['roleTypes'], 'ref_role_type_id', 'ref_role_type_description') ?>
                            </select>
                            <div class="invalid-feedback">Sila pilih jenis peranan.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="fieldStatus" class="form-label fw-medium small">Status</label>
                            <select class="form-select" id="fieldStatus" name="status_id" required>
                                <?php foreach ($view['statusOptions'] as $opt): ?>
                                    <option value="<?= e($opt['ref_status_id']) ?>" <?= (int) $opt['ref_status_id'] === $statusActive ? 'selected' : '' ?>><?= e($opt['ref_status_description']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Sila pilih status.</div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="inPassword" class="form-label fw-medium small">Kata Laluan <span class="text-muted fw-normal" id="pwdHint"></span></label>
                        <div class="position-relative">
                            <input type="password" class="form-control pe-5" id="inPassword" name="password" placeholder="Masukkan kata laluan" autocomplete="new-password" required>
                            <button type="button" class="btn-toggle-password" id="demoTogglePwd" aria-label="Tunjukkan kata laluan" style="position:absolute;top:50%;right:0.4rem;transform:translateY(-50%);width:30px;height:30px;border:none;background:transparent;color:var(--isisa-text-muted);">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Sila masukkan kata laluan.</div>
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

    <!-- Halaman Kakitangan: isi borang sunting, kira tarikh lahir/umur dari No. KP, tapis Seksyen mengikut Jabatan, papar mesej -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('employeeModal');
            var form = document.getElementById('employeeForm');
            var defaultStatus = <?= json_encode((string) $statusActive) ?>;
            var byId = function (id) { return document.getElementById(id); };
            var sectionSelect = byId('fieldSection');
            var sectionOptions = Array.prototype.slice.call(sectionSelect.options);

            // Papar hanya Seksyen di bawah Jabatan yang dipilih
            function filterSections(keep) {
                var dept = byId('fieldDepartment').value;
                sectionSelect.innerHTML = '';
                sectionOptions.forEach(function (o) {
                    if (o.value === '' || (dept !== '' && o.dataset.department === dept)) { sectionSelect.appendChild(o); }
                });
                sectionSelect.value = keep || '';
            }

            // Tarikh lahir & umur (paparan sahaja; pelayan mengira semula daripada No. KP)
            function updateDob() {
                var ic = byId('fieldMyID').value.replace(/\D/g, '');
                byId('fieldDob').value = '';
                byId('fieldAge').value = '';
                if (ic.length !== 12) { return; }
                var yy = +ic.slice(0, 2), mm = +ic.slice(2, 4), dd = +ic.slice(4, 6);
                var year = yy > (new Date().getFullYear() % 100) ? 1900 + yy : 2000 + yy;
                var dob = new Date(year, mm - 1, dd);
                if (dob.getMonth() !== mm - 1 || dob.getDate() !== dd) { return; }
                var now = new Date(), age = now.getFullYear() - year;
                if (now < new Date(now.getFullYear(), mm - 1, dd)) { age--; }
                byId('fieldDob').value = ('0' + dd).slice(-2) + '/' + ('0' + mm).slice(-2) + '/' + year;
                byId('fieldAge').value = age >= 0 ? age : '';
            }

            function fillForm(mode, d) {
                byId('empAction').value = mode;
                byId('empId').value = d.id || '';
                byId('fieldFullname').value = d.fullname || '';
                byId('fieldMyID').value = d.myid || '';
                byId('fieldEmail').value = d.email || '';
                byId('fieldPhone').value = d.phone || '';
                byId('fieldExt').value = d.ext || '';
                byId('fieldDepartment').value = d.department || '';
                filterSections(d.section);
                byId('fieldDivision').value = d.division || '';
                byId('fieldBranch').value = d.branch || '';
                byId('fieldRole').value = d.role || '';
                byId('fieldRoleType').value = d.roletype || '';
                byId('fieldStatus').value = d.status || defaultStatus;
                byId('inPassword').value = '';
                byId('inPassword').required = mode !== 'update';
                byId('pwdHint').textContent = mode === 'update' ? '(kosongkan jika tidak mahu tukar)' : '';
                byId('employeeModalLabel').textContent = mode === 'update' ? 'Sunting Kakitangan' : 'Tambah Kakitangan';
                form.classList.remove('was-validated');
                updateDob();
            }

            byId('fieldDepartment').addEventListener('change', function () { filterSections(''); });
            byId('fieldMyID').addEventListener('input', updateDob);
            byId('demoTogglePwd').addEventListener('click', function () {
                var f = byId('inPassword'), show = f.type === 'password';
                f.type = show ? 'text' : 'password';
                this.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            });

            byId('btnAddEmployee').addEventListener('click', function () { fillForm('create', {}); });
            document.querySelectorAll('.btn-edit-employee').forEach(function (btn) {
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
            // Simpan ditolak: buka semula borang dengan nilai yang ditaip (kata laluan tidak disimpan)
            fillForm(<?= json_encode((string) $old['action']) ?>, {
                id: <?= json_encode((string) $old['id']) ?>,
                myid: <?= json_encode((string) $old['myID']) ?>,
                fullname: <?= json_encode((string) $old['fullname']) ?>,
                email: <?= json_encode((string) $old['email']) ?>,
                phone: <?= json_encode((string) $old['phone_number']) ?>,
                ext: <?= json_encode((string) $old['office_extension']) ?>,
                department: <?= json_encode((string) $old['ref_department_id']) ?>,
                section: <?= json_encode((string) $old['ref_section_id']) ?>,
                division: <?= json_encode((string) $old['ref_division_id']) ?>,
                branch: <?= json_encode((string) $old['ref_branch_id']) ?>,
                role: <?= json_encode((string) $old['ref_role_id']) ?>,
                roletype: <?= json_encode((string) $old['ref_role_type_id']) ?>,
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