<?php
declare(strict_types=1);

// Start the system. require_once is safe here: when this page is shown by
// respondError() or by maintenance mode, the system is already (partly) running
// and nothing is loaded twice.
require_once dirname(__DIR__, 2) . '/core/config/bootstrap.php';

// This page is shown when something has FAILED, maybe while the system was still
// starting (or in maintenance mode, before the session is started). So it loads the
// few helpers it needs by itself instead of trusting that they are ready.
require_once CORE_PATH . '/helpers/stringHelpers.php';
require_once CORE_PATH . '/helpers/globalHelpers.php';
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// If a library file is missing, still show the page (without styles).
// An error inside the error page itself would only hide the real problem.
try {
    $headTags = fontLibrary('error') . cssLibrary('error')
        . '<link rel="stylesheet" href="' . e(asset('assets/css/error-css/main.css', true)) . '">';
    $bodyTags = jsLibrary('error')
        . '<script type="module" src="' . e(asset('assets/js/error-js/main.js', true)) . '"></script>';
} catch (Throwable) {
    $headTags = $bodyTags = '';
}

// ---------------------------------------------------------------
// This page shows EVERY "server" error (5xx): 500, 503, 502...
// (400.php shows the client errors, 4xx.)
// respondError() and maintenance mode set $statusCode. When Apache shows this
// page by itself (ErrorDocument in .htaccess), the status comes from REDIRECT_STATUS.
// ---------------------------------------------------------------
$statusCode = (int) ($statusCode ?? $_SERVER['REDIRECT_STATUS'] ?? 500);
if ($statusCode < 500 || $statusCode > 599) {
    $statusCode = 500;
}

// Text for each status:
// [icon, label in the pill, heading, description, help title, help text]
// In the help text, {link} becomes the help desk link and {time} the current time.
// Any 5xx code without its own line below (502, 504...) uses the 500 text.
$errorTexts = [
    500 => [
        'bi-bug',
        'Masalah Pelayan',
        'Sesuatu Telah Berlaku',
        'Maaf, berlaku masalah teknikal pada pihak kami semasa memproses permintaan anda. Ia bukan kesalahan anda. Sila cuba semula sebentar lagi.',
        'Masalah berterusan?',
        'Sila hubungi {link} dan nyatakan masa ralat ini berlaku ({time}).'
    ],
    503 => [
        'bi-tools',
        'Penyelenggaraan Sistem',
        'Sistem Dalam Penyelenggaraan',
        'Sistem ISISA sedang dikemas kini untuk meningkatkan perkhidmatan. Kami akan kembali beroperasi tidak lama lagi. Harap bersabar dan sila cuba semula sebentar lagi.',
        'Ada urusan segera?',
        'Sila hubungi {link} untuk mendapatkan bantuan.'
    ],
];
[$errorIcon, $errorLabel, $errorHeading, $errorText, $helpTitle, $helpText] = $errorTexts[$statusCode] ?? $errorTexts[500];

// While the maintenance setting is ON, show the message the admin saved (with the end time) instead of the standard text.
// function_exists: this page can also be shown by the .env switch, before the helpers are loaded.
if ($statusCode === 503 && function_exists('maintenanceCurrent') && ($maintenanceRow = maintenanceCurrent()) !== null) {
    $errorText = maintenanceNotice($maintenanceRow);
}

$helpLink ='<a href="https://ipis.mbsa.gov.my" class="link-subtle" target="_blank" rel="noopener">meja bantuan</a>';
$helpHtml = str_replace(['{link}', '{time}'], [$helpLink, e(date('d/m/Y h:i A'))], e($helpText));

// In maintenance mode (503) there is nothing to go "back" to, and logging in is
// blocked, so the page only offers "Try again".
$isMaintenance = $statusCode === 503;

// Make sure the browser gets the real status. The technical details are NOT shown
// here: they are only written to storage/logs/php-error.log.
if (!headers_sent()) {
    http_response_code($statusCode);
    header('Cache-Control: no-store'); // Never cache an error page, or it may stay after the problem is fixed
    if ($isMaintenance) {
        header('Retry-After: 3600');   // Tell browsers and search engines to come back in 1 hour
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="<?= e($statusCode . ' ' . $errorLabel) ?> | Sistem ISISA — Majlis Bandaraya Shah Alam">
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
            <!-- In maintenance mode the logo is not a link (the login page is not available) -->
            <<?= $isMaintenance ? 'span' : 'a href="' . e(loginUrl()) . '" aria-label="Kembali ke halaman utama ISISA"' ?> class="brand-link">
            <!-- NOTA LOGO MBSA: Gantikan URL di bawah dengan laluan fail lokal anda (cth: assets/img/logo-mbsa.svg) -->
            <img
                src="https://upload.wikimedia.org/wikipedia/commons/e/e9/Shah_Alam_Emblem.svg"
                alt="Jata Rasmi Majlis Bandaraya Shah Alam (MBSA)"
                class="brand-logo"
            >
            <div>
                <h1 class="system-title">ISISA</h1>
                <p class="system-subtitle mb-0">Majlis Bandaraya Shah Alam | Jabatan Kesihatan dan Kelestarian Sisa</p>
            </div>
            </<?= $isMaintenance ? 'span' : 'a' ?>>
        </header>

        <!-- Kandungan Utama (ralat 5xx) -->
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

                <?php if (!empty($errorMessage) && config('app.debug') && !$isMaintenance): ?>
                    <!-- For developers only: shown when APP_DEBUG=true, never in production -->
                    <p class="error-description"><code><?= e($errorMessage) ?></code></p>
                <?php endif; ?>

                <!-- Butang Tindakan Utama & Sekunder -->
                <div class="action-group">
                    <?php if (!$isMaintenance): ?>
                        <button type="button" class="btn btn-isisa-outline" id="btnGoBack" data-fallback="<?= e(loginUrl()) ?>">
                            <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            <span>Halaman Sebelumnya</span>
                        </button>
                    <?php endif; ?>
                    <a href="<?= e(currentUrl()) ?>" class="btn btn-isisa-primary">
                        <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                        <span>Cuba Semula</span>
                    </a>
                </div>

                <!-- Kad Bantuan Teknikal Ringkas -->
                <div class="help-card">
                    <div class="help-card-icon" aria-hidden="true">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div>
                        <h3 class="help-card-title"><?= e($helpTitle) ?></h3>
                        <p class="help-card-text">
                            <?= $helpHtml ?>
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
