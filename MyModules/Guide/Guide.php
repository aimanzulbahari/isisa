<?php

declare(strict_types=1);

// Start the system (config, session, CSRF, helpers, middleware)
require_once dirname(__DIR__, 2) . '/core/config/bootstrap.php';

// Library loaders for the third-party fonts, CSS and JS in /public
require_once PUBLIC_PATH . '/library/font-library.php';
require_once PUBLIC_PATH . '/library/css-library.php';
require_once PUBLIC_PATH . '/library/js-library.php';

// Page logic (see GuideController.php): reads README.md and turns it into HTML
require_once __DIR__ . '/GuideController.php';
$view = handleGuide();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Guide</title>

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

                <section class="row g-3 g-lg-4">
                    <div class="col-12 animate-in delay-1">
                        <div class="isisa-card p-3 p-md-4">
                            <div class="table-head mb-3">
                                <div>
                                    <h3 class="h6 fw-semibold mb-1">README.md</h3>
                                    <p class="text-muted small mb-0">
                                        <?php if ($view['source'] !== null): ?>
                                            Sumber: <?= e($view['source']) ?>
                                            <?php if ($view['updated'] !== null): ?>
                                                &middot; Dikemas kini <?= e(date('d M Y, H:i', $view['updated'])) ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Panduan penggunaan sistem
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <?php if ($view['html'] !== ''): ?>
                                <!-- HTML ini dijana oleh Parsedown dalam safe mode (tag HTML mentah dalam Markdown sudah di-escape) -->
                                <article class="markdown-body"><?= $view['html'] ?></article>
                            <?php else: ?>
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-journal-text fs-1 d-block mb-2" aria-hidden="true"></i>
                                    <?php if ($view['source'] !== null): ?>
                                        Fail <strong><?= e($view['source']) ?></strong> masih kosong.
                                    <?php else: ?>
                                        Fail <strong>README.md</strong> tidak ditemui. Letakkan di <code>MyModules/Guide/</code>.
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
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

</body>

</html>