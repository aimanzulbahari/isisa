<?php
declare(strict_types=1);

// Start the system. require_once is safe here: when this page is shown by
// respondError(), the system is already running and nothing is loaded twice.
require_once dirname(__DIR__, 2) . '/core/config/bootstrap.php';

// This page can be shown while the system is only half started, so it loads
// the few helpers it needs by itself instead of trusting that they are ready.
require_once CORE_PATH . '/helpers/stringHelpers.php';
require_once CORE_PATH . '/helpers/globalHelpers.php';
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// If a library file is missing, still show the page (without styles)
// instead of failing inside the error page itself.
try {
    $headTags = fontLibrary('error') . cssLibrary('error')
        . '<link rel="stylesheet" href="' . e(asset('assets/css/error-css/main.css', true)) . '">';
    $bodyTags = jsLibrary('error')
        . '<script type="module" src="' . e(asset('assets/js/error-js/main.js', true)) . '"></script>';
} catch (Throwable) {
    $headTags = $bodyTags = '';
}

// ---------------------------------------------------------------
// This page shows EVERY "client" error (4xx): 400, 401, 403, 404, 419...
// (500.php shows the server errors, 5xx.)
// respondError() sets $statusCode. When Apache shows this page by itself
// (ErrorDocument in .htaccess), the status comes from REDIRECT_STATUS.
// ---------------------------------------------------------------
$statusCode = (int) ($statusCode ?? $_SERVER['REDIRECT_STATUS'] ?? 400);
if ($statusCode < 400 || $statusCode > 499) {
    $statusCode = 400;
}

// Text for each status: [icon, label in the pill, heading, description]
// To support another 4xx code, add one line here.
$errorTexts = [
    400 => [
        'bi-slash-circle',
        'Permintaan Tidak Sah',
        'Permintaan Tidak Dapat Diproses',
        'Maaf, pelayan tidak dapat memahami permintaan anda. Data yang dihantar mungkin tidak lengkap, tidak sah, atau pautan yang digunakan telah rosak.'
    ],
    401 => [
        'bi-person-lock',
        'Log Masuk Diperlukan',
        'Sila Log Masuk Semula',
        'Anda perlu log masuk untuk meneruskan, atau sesi anda telah tamat. Sila log masuk semula.'
    ],
    403 => [
        'bi-shield-lock',
        'Akses Tidak Dibenarkan',
        'Akses Dilarang',
        'Maaf, anda tidak mempunyai kebenaran untuk mengakses halaman ini. Sila hubungi pentadbir sistem jika anda rasa ini satu kesilapan.'
    ],
    404 => [
        'bi-compass',
        'Pautan Tidak Sah',
        'Halaman Tidak Ditemui',
        'Maaf, halaman yang anda cuba capai tidak wujud, telah dipindahkan, atau alamat URL yang dimasukkan tidak tepat.'
    ],
    405 => [
        'bi-slash-circle',
        'Kaedah Tidak Dibenarkan',
        'Kaedah Permintaan Tidak Dibenarkan',
        'Maaf, halaman ini tidak menerima jenis permintaan yang dihantar. Sila kembali dan cuba semula.'
    ],
    419 => [
        'bi-clock-history',
        'Sesi Tamat Tempoh',
        'Sesi Telah Tamat Tempoh',
        'Borang yang anda hantar sudah tamat tempoh atau tidak sah atas sebab keselamatan. Sila muat semula halaman dan cuba semula.'
    ],
];
[$errorIcon, $errorLabel, $errorHeading, $errorText] = $errorTexts[$errorTextCode ?? $statusCode] ?? $errorTexts[400];

// Make sure the browser gets the real status
if (!headers_sent()) {
    http_response_code($statusCode);
    header('Cache-Control: no-store');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="<?= e($statusCode . ' ' . $errorLabel) ?> | Sistem ISISA — Majlis Bandaraya Shah Alam">
    <title><?= e($statusCode) ?> | ISISA</title>
    <!-- Favicon (see src/components/favicon.php) -->
    <?php require SRC_PATH . '/components/favicon.php'; ?>
    <!-- Font, libraries and page styles (see public/library/*.php) -->
    <?= $headTags ?>
</head>

<body>
    <!-- Latar Abstrak Halus -->
    <div class="bg-subtle-pattern" aria-hidden="true"></div>
    <div class="bg-soft-glow" aria-hidden="true"></div>
    <!-- Header Atas Kiri: Identiti MBSA & ISISA -->
    <header class="page-header animate-entry">
        <a href="<?= e(loginUrl()) ?>" class="brand-link" aria-label="Kembali ke halaman utama ISISA">
            <!-- NOTA LOGO MBSA: Gantikan URL di bawah dengan laluan fail lokal anda (cth: assets/img/logo-mbsa.svg) -->
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e9/Shah_Alam_Emblem.svg"
                alt="Jata Rasmi Majlis Bandaraya Shah Alam (MBSA)" class="brand-logo">
            <div>
                <h1 class="system-title">ISISA</h1>
                <p class="system-subtitle mb-0">Majlis Bandaraya Shah Alam | Jabatan Kesihatan dan Kelestarian Sisa</p>
            </div>
        </a>
    </header>

    <!-- Kandungan Utama (ralat 4xx) -->
    <main class="error-main">
        <div class="error-container animate-entry">

            <div class="error-visual-wrapper">
                <div class="error-icon-box" aria-hidden="true">
                    <i class="bi <?= e($errorIcon) ?>"></i>
                </div>
            </div>

            <div>
                <span class="error-status-pill">
                    <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                    <span>Ralat <?= e($statusCode) ?> — <?= e($errorLabel) ?></span>
                </span>
            </div>

            <div class="error-code" aria-hidden="true"><?= e($statusCode) ?></div>

            <h2 class="error-heading"><?= e($errorHeading) ?></h2>
            <p class="error-description">
                <?= e($errorText) ?>
            </p>

            <!-- Butang Tindakan Utama & Sekunder -->
            <div class="action-group">
                <button type="button" class="btn btn-isisa-outline" id="btnGoBack" data-fallback="<?= e(loginUrl()) ?>">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span>Halaman Sebelumnya</span>
                </button>
                <a href="<?= e(loginUrl()) ?>" class="btn btn-isisa-primary">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    <span>Kembali ke Log Masuk</span>
                </a>
            </div>

            <!-- Kad Bantuan Teknikal Ringkas -->
            <div class="help-card">
                <div class="help-card-icon" aria-hidden="true">
                    <i class="bi bi-headset"></i>
                </div>
                <div>
                    <h3 class="help-card-title">Perlukan bantuan capaian sistem?</h3>
                    <p class="help-card-text">
                        Sekiranya masalah ini berterusan, sila hubungi <a href="https://ipis.mbsa.gov.my"
                            class="link-subtle" target="_blank" rel="noopener">meja bantuan</a>.
                    </p>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer Hak Cipta -->
    <footer class="page-footer">
        <p class="footer-copyright mb-0">&copy; 2026 Majlis Bandaraya Shah Alam. Hak cipta terpelihara.</p>
    </footer>

    <!-- Libraries and page scripts (see public/library/*.php) -->
    <?= $bodyTags ?>
</body>

</html>