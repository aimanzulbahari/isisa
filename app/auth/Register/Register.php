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

// UI SAHAJA: salinan dari Screen-Lock.php, belum ada logik pendaftaran (RegisterController.php masih kosong).
// Tiada mesej pelayan untuk dipaparkan.
$login = ['type' => '', 'message' => ''];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Log masuk ke Sistem ISISA - Majlis Bandaraya Shah Alam">
    <title>Daftar Akaun | ISISA</title>
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
            BORANG DAFTAR AKAUN (penuh lebar, tanpa panel kanan)
            ==================================================================== -->
            <section class="col-12 form-column" aria-label="Borang Daftar Akaun Sistem ISISA">
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
                    <!-- Lebih lebar daripada borang log masuk (420px) kerana ada dua lajur medan -->
                    <div class="form-container animate-entry" style="max-width: 680px;">

                        <!-- Tajuk Daftar Akaun -->
                        <div class="mb-4">
                            <h2 class="login-heading">Daftar Akaun</h2>
                            <p class="login-subheading mb-0">Isi maklumat anda untuk mendaftar akaun ISISA.</p>
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


                        <form method="post" action="<?= e(routeUrl('register')) ?>" class="needs-validation" novalidate>

                            <!-- CSRF token: required, or the server rejects the form with error 419 -->
                            <?= csrfField() ?>

                            <div class="row g-3 mb-4">
                                <!-- 1. No. Kad Pengenalan (digit sahaja, ditapis oleh auth-js/modules/ic-input.js) -->
                                <div class="col-12 col-md-6">
                                    <label for="noKadPengenalan" class="form-label">No. Kad Pengenalan</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-credit-card-2-front input-icon-left" aria-hidden="true"></i>
                                        <input
                                            type="text"
                                            class="form-control form-control-custom"
                                            id="noKadPengenalan"
                                            name="no_kp"
                                            placeholder="12 Digit No.Kad Pengenalan"
                                            inputmode="numeric"
                                            maxlength="12"
                                            pattern="\d{12}"
                                            autocomplete="off"
                                            required>
                                    </div>
                                    <div class="invalid-feedback">Sila masukkan 12 digit No. Kad Pengenalan yang sah (nombor sahaja).</div>
                                </div>

                                <!-- 2. Nama Penuh -->
                                <div class="col-12 col-md-6">
                                    <label for="namaPenuh" class="form-label">Nama Penuh</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-person input-icon-left" aria-hidden="true"></i>
                                        <input
                                            type="text"
                                            class="form-control form-control-custom"
                                            id="namaPenuh"
                                            name="fullname"
                                            placeholder="Seperti dalam Kad Pengenalan"
                                            maxlength="255"
                                            autocomplete="name"
                                            required>
                                    </div>
                                    <div class="invalid-feedback">Nama penuh wajib diisi.</div>
                                </div>

                                <!-- 3. Umur (baca sahaja, dikira daripada No. Kad Pengenalan) -->
                                <div class="col-12 col-md-6">
                                    <label for="umur" class="form-label">Umur</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-hourglass-split input-icon-left" aria-hidden="true"></i>
                                        <input
                                            type="text"
                                            class="form-control form-control-custom"
                                            id="umur"
                                            name="age"
                                            placeholder="Dikira automatik"
                                            readonly
                                            tabindex="-1"
                                            aria-describedby="umurHelp">
                                    </div>
                                    <div id="umurHelp" class="form-text text-muted small mt-1">Dikira daripada No. Kad Pengenalan.</div>
                                </div>

                                <!-- 4. Tarikh Lahir (baca sahaja, diambil daripada No. Kad Pengenalan) -->
                                <div class="col-12 col-md-6">
                                    <label for="tarikhLahir" class="form-label">Tarikh Lahir</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-calendar-event input-icon-left" aria-hidden="true"></i>
                                        <input
                                            type="text"
                                            class="form-control form-control-custom"
                                            id="tarikhLahir"
                                            name="dob"
                                            placeholder="Dikira automatik"
                                            readonly
                                            tabindex="-1"
                                            aria-describedby="tarikhLahirHelp">
                                    </div>
                                    <div id="tarikhLahirHelp" class="form-text text-muted small mt-1">Diambil daripada No. Kad Pengenalan.</div>
                                </div>

                                <!-- 5. No. Telefon -->
                                <div class="col-12 col-md-6">
                                    <label for="noTelefon" class="form-label">No. Telefon</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-telephone input-icon-left" aria-hidden="true"></i>
                                        <input
                                            type="tel"
                                            class="form-control form-control-custom"
                                            id="noTelefon"
                                            name="phone_number"
                                            placeholder="cth: 012-3456789"
                                            inputmode="tel"
                                            maxlength="20"
                                            autocomplete="tel"
                                            required>
                                    </div>
                                    <div class="invalid-feedback">No. telefon wajib diisi.</div>
                                </div>

                                <!-- 6. Emel -->
                                <div class="col-12 col-md-6">
                                    <label for="emel" class="form-label">Emel</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-envelope input-icon-left" aria-hidden="true"></i>
                                        <input
                                            type="email"
                                            class="form-control form-control-custom"
                                            id="emel"
                                            name="email"
                                            placeholder="nama@contoh.com"
                                            maxlength="150"
                                            autocomplete="email"
                                            required>
                                    </div>
                                    <div class="invalid-feedback">Sila masukkan alamat emel yang sah.</div>
                                </div>

                                <!-- 7. Kata Laluan -->
                                <div class="col-12 col-md-6">
                                    <label for="kataLaluan" class="form-label">Kata Laluan</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-lock input-icon-left" aria-hidden="true"></i>
                                        <input
                                            type="password"
                                            class="form-control form-control-custom has-right-btn"
                                            id="kataLaluan"
                                            name="password"
                                            placeholder="Masukkan kata laluan anda"
                                            autocomplete="new-password"
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

                                <!-- 8. Jawatan -->
                                <div class="col-12 col-md-6">
                                    <label for="jawatan" class="form-label">Jawatan</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-briefcase input-icon-left" aria-hidden="true"></i>
                                        <input
                                            type="text"
                                            class="form-control form-control-custom"
                                            id="jawatan"
                                            name="position"
                                            placeholder="cth: Pegawai Kesihatan Persekitaran"
                                            maxlength="150"
                                            autocomplete="organization-title"
                                            required>
                                    </div>
                                    <div class="invalid-feedback">Jawatan wajib diisi.</div>
                                </div>
                            </div>

                            <!-- Butang Daftar Akaun -->
                            <button type="submit" class="btn btn-isisa-primary w-100">
                                Daftar Akaun
                            </button>
                        </form>

                        <!-- Pautan kembali ke halaman log masuk -->
                        <p class="text-center mt-4 mb-0" style="font-size: 0.875rem; color: var(--isisa-text-muted);">
                            Sudah ada akaun?
                            <a href="<?= e(routeUrl('login')) ?>" class="link-subtle fw-semibold ms-1">Log masuk</a>
                        </p>
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
