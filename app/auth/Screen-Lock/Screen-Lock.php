<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 3) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Only logged-in users may see this page (a visitor is sent to /Login)
requireAuth();

// Lock-screen logic (see ScreenLockController.php). On a valid POST this redirects and never returns.
require_once __DIR__ . '/ScreenLockController.php';
$login = handleScreenLock(); // ['type' => alert type, 'message' => text, 'name' => the user's name]
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Log masuk ke Sistem ISISA - Majlis Bandaraya Shah Alam">
    <title>Kunci Skrin | ISISA</title>
    <!-- Favicon (see src/components/favicon.php) -->
    <?php require SRC_PATH . '/components/favicon.php'; ?>
    <!-- Font: Plus Jakarta Sans (local file, see public/library/font-library.php) -->
    <?= fontLibrary('auth') ?>
    <!-- Libraries: Bootstrap & Bootstrap Icons (see public/library/css-library.php) -->
    <?= cssLibrary('auth') ?>
    <!-- Page styles: must come AFTER the libraries so they can override them -->
    <link rel="stylesheet" href="<?= e(asset('assets/css/auth-css/main.css', true)) ?>">
</head>

<body>

    <main class="container-fluid p-0">
        <div class="row g-0 login-wrapper">
            <!-- ====================================================================
            BORANG KUNCI SKRIN (penuh lebar, tanpa panel kanan)
            ==================================================================== -->
            <section class="col-12 form-column" aria-label="Borang Log Masuk Sistem ISISA">
                <!-- 1. Logo MBSA & Tajuk Sistem (atas kiri) -->
                <header class="form-header animate-entry">
                    <div class="d-flex align-items-center gap-3">
                        <!-- NOTA LOGO MBSA: Gantikan URL di bawah dengan laluan fail lokal anda (cth: assets/img/logo-mbsa.svg) -->
                        <img
                            src="https://upload.wikimedia.org/wikipedia/commons/e/e9/Shah_Alam_Emblem.svg"
                            alt="Jata Rasmi Majlis Bandaraya Shah Alam (MBSA)"
                            class="brand-logo">
                        <div>
                            <h1 class="system-title">ISISA | MBSA</h1>
                            <p class="system-subtitle mb-0">Jabatan Kesihatan dan Kelestarian Sisa</p>
                        </div>
                    </div>
                </header>

                <div class="form-main">
                    <div class="form-container animate-entry">

                        <!-- Tajuk Log Masuk -->
                        <div class="mb-4">
                            <h2 class="login-heading">Kunci Skrin</h2>
                            <p class="login-subheading mb-0">Masukkan Kata Laluan Anda.</p>
                        </div>

                        <!-- Mesej daripada pelayan (ralat log masuk, sesi tamat, dll.) -->
                        <!-- Shown as a popup by auth-js/modules/notice-popup.js (SweetAlert2).
                                 If the script cannot run, this box stays visible as a normal message. -->
                        <?php if ($login['message'] !== ''): ?>
                            <div id="serverNotice"
                                class="alert alert-<?= e($login['type']) ?> mt-3 mb-0"
                                role="alert"
                                data-type="<?= e($login['type']) ?>"
                                data-message="<?= e($login['message']) ?>">
                                <?= e($login['message']) ?>
                            </div>
                        <?php endif; ?>

                      
                        <form method="post" action="<?= e(routeUrl('lock-screen')) ?>" class="needs-validation" novalidate>

                            <!-- CSRF token: required, or the server rejects the form with error 419 -->
                            <?= csrfField() ?>
                            <div class="mb-2">
                                <label for="kataLaluan" class="form-label">Kata Laluan</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-lock input-icon-left" aria-hidden="true"></i>
                                    <input
                                        type="password"
                                        class="form-control form-control-custom has-right-btn"
                                        id="kataLaluan"
                                        name="password"
                                        placeholder="Masukkan kata laluan anda"
                                        autocomplete="current-password"
                                        aria-describedby="passwordFeedback"
                                        required>
                                    <button
                                        type="button"
                                        class="btn-toggle-password"
                                        id="togglePasswordBtn"
                                        aria-label="Tunjukkan kata laluan"
                                        aria-pressed="false">
                                        <i class="bi bi-eye" id="togglePasswordIcon" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div id="passwordFeedback" class="invalid-feedback">
                                    Kata laluan wajib diisi.
                                </div>
                            </div>

                            <!-- 7. Butang Log Masuk Utama -->
                            <button type="submit" class="btn btn-isisa-primary w-100">
                                Buka Skrin
                            </button>
                        </form>
                        <!-- 9. Footer Hak Cipta -->
                        <footer class="mt-4 pt-3 text-center">
                            <p class="footer-copyright mb-0">&copy; 2026 Majlis Bandaraya Shah Alam. Hak cipta terpelihara.</p>
                        </footer>

                    </div>
                </div>
            </section>

        </div>
    </main>
    <!-- Libraries: Bootstrap bundle (see public/library/js-library.php) -->
    <?= jsLibrary('auth') ?>
    <!-- Page scripts: type="module" because main.js imports its modules -->
    <script type="module" src="<?= e(asset('assets/js/auth-js/main.js', true)) ?>"></script>

</body>

</html>