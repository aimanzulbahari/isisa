<?php
declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 3) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Only visitors who are not logged in may see this page
requireGuest();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Log masuk ke Sistem ISISA - Majlis Bandaraya Shah Alam">
    <title>Lupa Kata Laluan | ISISA</title>
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
            COLUMN KIRI (col-lg-5): BORANG LOG MASUK
            ==================================================================== -->
            <section class="col-12 col-lg-5 form-column" aria-label="Borang Log Masuk Sistem ISISA">
                <!-- 1. Logo MBSA & Tajuk Sistem (atas kiri) -->
                <header class="form-header animate-entry">
                    <div class="d-flex align-items-center gap-3">
                        <!-- NOTA LOGO MBSA: Gantikan URL di bawah dengan laluan fail lokal anda (cth: assets/img/logo-mbsa.svg) -->
                        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e9/Shah_Alam_Emblem.svg"
                            alt="Jata Rasmi Majlis Bandaraya Shah Alam (MBSA)" class="brand-logo">
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
                            <h2 class="login-heading">Lupa Kata Laluan</h2>
                            <p class="login-subheading mb-0">Masukkan Emel Yang Telah Didaftarkan</p>
                        </div>
                        <form method="post" action="login.php" class="needs-validation" novalidate>
                            <!-- CSRF token: required, or the server rejects the form with error 419 -->
                            <?= csrfField() ?>
                            <div class="mb-3">
                                <label for="noKadPengenalan" class="form-label">Sila Masukkan Emel</label>
                                <div class="input-icon-wrapper">
                                    <i class="bi bi-credit-card-2-front input-icon-left" aria-hidden="true"></i>
                                    <input type="text" class="form-control form-control-custom" id="noKadPengenalan"
                                        name="no_kp" placeholder="noreply@mail.com" inputmode="numeric"
                                        maxlength="12" pattern="\d{12}" autocomplete="username"
                                        aria-describedby="kpHelp kpFeedback" required>
                                </div>
                                <div id="kpHelp" class="form-text text-muted small mt-1">Alamat emel yang sah</div>
                                <div id="kpFeedback" class="invalid-feedback">
                                    Sila masukkan alamat emel yang sah.
                                </div>
                            </div>

                            <!-- 7. Butang Log Masuk Utama -->
                            <button type="submit" class="btn btn-isisa-primary w-100">
                                Hantar Pautan
                            </button>
                        </form>

                        <p class="text-center mt-4 mb-0" style="font-size: 0.875rem; color: var(--isisa-text-muted);">
                            Kembali Ke
                            <a href="<?= e(routeUrl('login')) ?>" class="link-subtle fw-semibold ms-1">Log Masuk</a>
                        </p>

                        <!-- 9. Footer Hak Cipta -->
                        <footer class="mt-4 pt-3 text-center">
                            <p class="footer-copyright mb-0">&copy; 2026 Majlis Bandaraya Shah Alam. Hak cipta
                                terpelihara.</p>
                        </footer>
                    </div>
                </div>
            </section>

            <!-- ====================================================================
            COLUMN KANAN (col-lg-7, DESKTOP SAHAJA): BRANDING & ANNOUNCEMENT CARD
            ==================================================================== -->
            <?php require SRC_PATH . '/components/auth-right-panel.php'; ?>

        </div>
    </main>
    <!-- Libraries: Bootstrap bundle (see public/library/js-library.php) -->
    <?= jsLibrary('auth') ?>
    <!-- Page scripts: type="module" because main.js imports its modules -->
    <script type="module" src="<?= e(asset('assets/js/auth-js/main.js', true)) ?>"></script>

</body>

</html>