<?php
declare(strict_types=1);

/**
 * Favicon component (the small icon shown in the browser tab).
 *
 * Use it inside <head>, after bootstrap.php has been loaded:
 *   <?php require SRC_PATH . '/components/favicon.php'; ?>
 *
 * The icon file is public/favicon/favicon.svg. SVG icons stay sharp at any size.
 * To support other icon files later (e.g. favicon.ico for old browsers),
 * add another <link> line below.
 */
$faviconFile = 'favicon/favicon.svg';

// Print nothing (instead of a broken link) if the file is missing
if (is_file(PUBLIC_PATH . '/' . $faviconFile)):
    ?>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset($faviconFile, true)) ?>">
<?php endif; ?>