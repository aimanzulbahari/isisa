<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 4) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see MaintenanceController.php): POST saves and redirects, GET returns the data to show
require_once __DIR__ . '/MaintenanceController.php';
$view = handleMaintenance();
$form = $view['form'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Penyelenggaraan Sistem</title>

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
                                <!-- PHP: Nama pengguna di bawah ialah placeholder. Gantikan dengan sesi PHP & lindungi dengan htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8') -->
                                <h2 class="welcome-title">
                                    Tetapan Penyelengaraan Sistem
                                </h2>
                                <p class="mb-0 text-white text-opacity-75" style="font-size: 0.925rem;">
                                    Tetapan untuk penyelengaraan sistem
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="row g-3 g-lg-4" aria-label="Penyelenggaraan Sistem">
                    <div class="col-12 animate-in delay-1">
                        <div class="isisa-card p-3 p-md-4">
                            <!-- Dihantar melalui POST bersama CSRF token; semua input disahkan semula di MaintenanceController.php -->
                            <form class="form-isisa needs-validation" id="maintenanceForm" method="post" action="<?= e(routeUrl('system-maintenance')) ?>" novalidate>
                                <?= csrfField() ?>

                                <!-- Status penyelenggaraan -->
                                <div class="mb-4">
                                    <div class="isisa-choice-title" id="maintStatusLabel">Set Status Penyelenggaraan<span class="req" aria-hidden="true">*</span></div>
                                    <div class="isisa-choice-row" style="--cols: 2" role="radiogroup" aria-labelledby="maintStatusLabel" aria-required="true">
                                        <label class="isisa-radio is-tile" for="maintInactive">
                                            <input type="radio" id="maintInactive" name="status_penyelenggaraan" value="tidak-aktif" <?= $form['status'] !== 'aktif' ? 'checked' : '' ?> required>
                                            <span class="isisa-radio-dot" aria-hidden="true"></span>
                                            <span class="isisa-check-label">Penyelenggaraan Tidak Aktif</span>
                                        </label>
                                        <label class="isisa-radio is-tile" for="maintActive">
                                            <input type="radio" id="maintActive" name="status_penyelenggaraan" value="aktif" <?= $form['status'] === 'aktif' ? 'checked' : '' ?> required>
                                            <span class="isisa-radio-dot" aria-hidden="true"></span>
                                            <span class="isisa-check-label">Penyelenggaraan Aktif</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Tempoh & pegawai -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="maintStart" class="form-label">Tarikh &amp; Masa Mula <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="maintStart" name="tarikh_mula" value="<?= e($form['start']) ?>" required>
                                        <div class="invalid-feedback">Sila pilih tarikh dan masa mula.</div>
                                    </div>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label for="maintEnd" class="form-label">Tarikh &amp; Masa Tamat <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="maintEnd" name="tarikh_tamat" value="<?= e($form['end']) ?>" required>
                                        <div class="invalid-feedback">Sila pilih tarikh dan masa tamat.</div>
                                    </div>
                                    <div class="col-12 col-xl-4">
                                        <label for="maintOfficer" class="form-label">Pegawai Yang Bertanggungjawab</label>
                                        <!-- Pengguna yang sedang log masuk; dirakam automatik di pelayan, bukan dihantar daripada borang -->
                                        <input type="text" class="form-control" id="maintOfficer" value="<?= e($view['officer']) ?>" readonly>
                                    </div>
                                </div>

                                <!-- Mesej -->
                                <div class="mb-4">
                                    <label for="maintMessage" class="form-label">Mesej Penyelenggaraan</label>
                                    <textarea class="form-control" id="maintMessage" name="mesej" rows="4" maxlength="500" placeholder="Tulis mesej yang akan dipaparkan kepada pengguna semasa penyelenggaraan..."><?= e($form['message']) ?></textarea>
                                    <div class="form-text">Maksimum 500 aksara.</div>
                                </div>

                                <!-- Peranan -->
                                <div class="mb-4">
                                    <div class="isisa-choice-title" id="maintRolesLabel">Peranan Yang Boleh Akses Sistem</div>
                                    <div class="isisa-choice-row" style="--cols: 3" role="group" aria-labelledby="maintRolesLabel">
                                        <!-- Satu kotak pilihan bagi setiap peranan dalam ref_role. Superadmin sentiasa dibenarkan (tidak boleh dinyahtanda). -->
                                        <?php foreach ($view['roles'] as $role):
                                            $code      = strtolower($role['ref_role_code']);
                                            $isSuper   = $code === 'superadmin';
                                            $isChecked = $isSuper || in_array($code, $form['roles'], true);
                                        ?>
                                            <label class="isisa-check is-tile" for="role-<?= e($code) ?>">
                                                <input type="checkbox" id="role-<?= e($code) ?>" name="peranan[]" value="<?= e($code) ?>" <?= $isChecked ? 'checked' : '' ?> <?= $isSuper ? 'onclick="return false;" aria-disabled="true"' : '' ?>>
                                                <span class="isisa-check-box" aria-hidden="true"><svg viewBox="0 0 24 24">
                                                        <path d="M5.5 12.5l4.2 4.2L18.5 8" />
                                                    </svg></span>
                                                <span class="isisa-check-label"><?= e($role['ref_role_description']) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Pengesahan -->
                                <div class="mb-4">
                                    <label class="isisa-check is-tile" for="maintDeclare">
                                        <input type="checkbox" id="maintDeclare" name="pengesahan" value="1" required>
                                        <span class="isisa-check-box" aria-hidden="true"><svg viewBox="0 0 24 24">
                                                <path d="M5.5 12.5l4.2 4.2L18.5 8" />
                                            </svg></span>
                                        <span class="isisa-check-label">Saya mengesahkan bahawa semua maklumat dan dokumen yang diserahkan adalah benar dan sah mengikut dasar MBSA.</span>
                                    </label>
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary toolbar-ctl"><i class="bi bi-send" aria-hidden="true"></i><span>Kemaskini Penyelengaraan</span></button>
                                </div>
                            </form>
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