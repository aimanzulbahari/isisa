<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 4) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see InfoController.php): POST saves and redirects, GET returns the data to show
require_once __DIR__ . '/InfoController.php';
$view = handleInfo();

$info = $view['info'];
$old  = $view['old'];
$php  = $view['php'];

// Value of a form field: what the user typed (after an error), else the saved value, else a default
$val = static function (string $key, string $default = '') use ($info, $old): string {
    return (string) ($old[$key] ?? $info[$key] ?? $default);
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Info Sistem</title>

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
                                <h2 class="welcome-title">
                                    Info Mengenai Sistem
                                </h2>
                                <p class="mb-0 text-white text-opacity-75" style="font-size: 0.925rem;">
                                    Maklumat konfigurasi sistem
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 4 Kad Ringkasan (Grid: 4 lajur desktop, 2 lajur tablet, 1 lajur mobile) -->
                <section class="row g-3 g-lg-4 mb-4" aria-label="Ringkasan Info Sistem">
                    <!-- Kad 1 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-1">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-hdd-stack" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= $info !== null ? e('v' . $info['system_version']) : '-' ?></div>
                                <div class="stat-label">Versi Sistem</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad 2 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-2">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-filetype-php" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e(PHP_VERSION) ?></div>
                                <div class="stat-label">Versi PHP</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kad 3 -->
                    <div class="col-12 col-sm-6 col-xl-3 animate-in delay-3">
                        <div class="isisa-card stat-card d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon-circle">
                                    <i class="bi bi-database" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div>
                                <div class="stat-value tabular-nums mb-1"><?= e($view['dbVersion']) ?></div>
                                <div class="stat-label">Pangkalan Data</div>
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
                                <div class="stat-value tabular-nums mb-1"><?= $info !== null ? e(date('d M Y', strtotime((string) $info['updated_at']))) : '-' ?></div>
                                <div class="stat-label">Kemaskini Terakhir</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="row g-3 g-lg-4" aria-label="Maklumat Sistem">

                    <!-- Kad Borang Info Sistem -->
                    <div class="col-12 col-xl-7 animate-in delay-4">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-4">
                                <h3 class="h6 fw-semibold mb-1">Maklumat Sistem &amp; Organisasi</h3>
                                <p class="text-muted small mb-0">Kemas kini maklumat asas sistem dan organisasi</p>
                            </div>

                            <!-- POST ke pelayan bersama CSRF token; pelayan mengesahkan semua input semula -->
                            <form class="form-isisa needs-validation" id="systemInfoForm" method="post" action="<?= e(routeUrl('system-info')) ?>" novalidate enctype="multipart/form-data">
                                <?= csrfField() ?>

                                <div class="form-group-title">Maklumat Sistem</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-8">
                                        <label for="systemName" class="form-label">Nama Sistem</label>
                                        <input type="text" class="form-control" id="systemName" name="system_name" value="<?= e($val('system_name')) ?>" maxlength="150" required>
                                        <div class="invalid-feedback">Sila masukkan nama sistem.</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="systemAcronym" class="form-label">Akronim Sistem</label>
                                        <input type="text" class="form-control" id="systemAcronym" name="system_short_name" value="<?= e($val('system_short_name')) ?>" maxlength="50" required>
                                        <div class="invalid-feedback">Sila masukkan akronim sistem.</div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <label for="systemLogo" class="form-label">Logo Sistem</label>
                                        <div class="logo-field">
                                            <div class="logo-preview" id="systemLogoPreview">
                                                <?php if (!empty($info['logo_path'])): ?>
                                                    <img src="<?= e(asset($info['logo_path'], true)) ?>" alt="Logo sistem semasa">
                                                <?php else: ?>
                                                    <i class="bi bi-image" aria-hidden="true"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <input type="file" class="form-control" id="systemLogo" name="system_logo" accept=".png,.jpg,.jpeg,.webp">
                                                <div class="form-text">PNG, JPG atau WEBP. Maksimum 2 MB.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="systemVersion" class="form-label">Versi Sistem</label>
                                        <input type="text" class="form-control" id="systemVersion" name="system_version" value="<?= e($val('system_version', '1.0.0')) ?>" maxlength="20" placeholder="cth: 1.0.0" required>
                                        <div class="invalid-feedback">Sila masukkan versi sistem.</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="agencyLogo" class="form-label">Logo Agensi</label>
                                        <div class="logo-field">
                                            <div class="logo-preview" id="agencyLogoPreview">
                                                <?php if (!empty($info['agency_logo_path'])): ?>
                                                    <img src="<?= e(asset($info['agency_logo_path'], true)) ?>" alt="Logo agensi semasa">
                                                <?php else: ?>
                                                    <i class="bi bi-image" aria-hidden="true"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <input type="file" class="form-control" id="agencyLogo" name="agency_logo" accept=".png,.jpg,.jpeg,.webp">
                                                <div class="form-text">PNG, JPG atau WEBP. Maksimum 2 MB.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group-title">Maklumat Organisasi</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-8">
                                        <label for="orgName" class="form-label">Nama Organisasi</label>
                                        <input type="text" class="form-control" id="orgName" name="organization_name" value="<?= e($val('organization_name')) ?>" maxlength="255" required>
                                        <div class="invalid-feedback">Sila masukkan nama organisasi.</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="orgDepartment" class="form-label">Jabatan</label>
                                        <select class="form-select" id="orgDepartment" name="system_department">
                                            <option value="">- Tiada -</option>
                                            <?php foreach ($view['departments'] as $dept): ?>
                                                <option value="<?= e($dept['ref_department_id']) ?>" <?= $val('system_department') === (string) $dept['ref_department_id'] ? 'selected' : '' ?>><?= e($dept['ref_department_code']) ?> - <?= e($dept['ref_department_description']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="orgPhone" class="form-label">No. Telefon</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-telephone" aria-hidden="true"></i></span>
                                            <input type="tel" class="form-control" id="orgPhone" name="support_phone" value="<?= e($val('support_phone')) ?>" maxlength="50" inputmode="tel" placeholder="03-1234 5678">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="orgEmail" class="form-label">Emel</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                                            <input type="email" class="form-control" id="orgEmail" name="support_email" value="<?= e($val('support_email')) ?>" maxlength="150" placeholder="nama@mbsa.gov.my" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="orgCopyright" class="form-label">Teks Hak Cipta</label>
                                        <input type="text" class="form-control" id="orgCopyright" name="copyright_text" value="<?= e($val('copyright_text')) ?>" maxlength="255" placeholder="cth: &copy; 2026 Majlis Bandaraya Shah Alam">
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary toolbar-ctl"><i class="bi bi-send" aria-hidden="true"></i><span>Kemaskini Info Sistem</span></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Kad Dropdown Maklumat PHP -->
                    <div class="col-12 col-xl-5 animate-in delay-5">
                        <div class="isisa-card p-3 p-md-4 h-100">
                            <div class="mb-3">
                                <h3 class="h6 fw-semibold mb-1">Maklumat PHP</h3>
                                <p class="text-muted small mb-0">Konfigurasi dan sambungan PHP pada pelayan</p>
                            </div>

                            <div class="accordion accordion-isisa accordion-static" id="phpInfoAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" tabindex="-1" aria-expanded="true" aria-controls="phpGeneral">
                                            <i class="bi bi-info-circle acc-icon" aria-hidden="true"></i>Maklumat Umum
                                        </button>
                                    </h2>
                                    <div id="phpGeneral" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <dl class="row mb-0 small php-info-list">
                                                <?php foreach ($php['general'] as $label => $value): ?>
                                                    <dt class="col-5"><?= e($label) ?></dt>
                                                    <dd class="col-7"><?= e($value) ?></dd>
                                                <?php endforeach; ?>
                                            </dl>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" tabindex="-1" aria-expanded="true" aria-controls="phpConfig">
                                            <i class="bi bi-sliders acc-icon" aria-hidden="true"></i>Konfigurasi
                                        </button>
                                    </h2>
                                    <div id="phpConfig" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <dl class="row mb-0 small php-info-list">
                                                <?php foreach ($php['config'] as $label => $value): ?>
                                                    <dt class="col-5"><?= e($label) ?></dt>
                                                    <dd class="col-7"><?= e($value) ?></dd>
                                                <?php endforeach; ?>
                                            </dl>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" tabindex="-1" aria-expanded="true" aria-controls="phpExtensions">
                                            <i class="bi bi-puzzle acc-icon" aria-hidden="true"></i>Sambungan (Extensions)
                                        </button>
                                    </h2>
                                    <div id="phpExtensions" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php foreach ($php['extensions'] as $name => $loaded): ?>
                                                    <span class="badge-status badge-status-<?= $loaded ? 'success' : 'neutral' ?>"><?= e($name) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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

    <!-- Sembunyikan ikon tanda semak (sah) pada medan; ikon ralat (merah) kekal -->
    <style>
        #systemInfoForm .form-control:valid,
        #systemInfoForm .form-control.is-valid {
            background-image: none;
            padding-right: 0.75rem;
        }
        #systemInfoForm .form-select:valid,
        #systemInfoForm .form-select.is-valid {
            background-image: var(--bs-form-select-bg-img);
            padding-right: 2.25rem;
        }
    </style>

    <!-- Halaman Info Sistem: semakan ringkas, pratonton imej, papar mesej (toastr) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('systemInfoForm');

            // Semakan ringkas sebelum hantar (pelayan tetap mengesahkan semula)
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.classList.add('was-validated');
                    var bad = form.querySelector(':invalid');
                    if (typeof toastr !== 'undefined') {
                        var label = bad && bad.id ? form.querySelector('label[for="' + bad.id + '"]') : null;
                        toastr.error(label ? 'Sila betulkan medan: ' + label.textContent.replace('*', '').trim() : 'Sila lengkapkan medan yang diperlukan.');
                    }
                    if (bad) bad.focus();
                }
            });

            // Pratonton imej yang dipilih (sebelum disimpan)
            function preview(inputId, boxId) {
                var input = document.getElementById(inputId);
                var box = document.getElementById(boxId);
                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    if (!file || file.type.indexOf('image/') !== 0) return;
                    var img = document.createElement('img');
                    img.alt = 'Pratonton';
                    img.src = URL.createObjectURL(file);
                    box.replaceChildren(img);
                });
            }
            preview('systemLogo', 'systemLogoPreview');
            preview('agencyLogo', 'agencyLogoPreview');

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
