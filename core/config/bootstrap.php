<?php
declare(strict_types=1);

/**
 * Application bootstrap (the starting point of every request).
 *
 * Include this file at the top of any entry page:
 *   require_once __DIR__ . '/core/config/bootstrap.php';
 */

// Load the config and database helpers
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

// URL helpers are needed very early (maintenance page, redirects), so load them first
require_once CORE_PATH . '/helpers/urlHelpers.php';

// Set the default timezone
date_default_timezone_set((string) config('app.timezone'));

// ---------------------------------------------------------------
// Error handling
// ---------------------------------------------------------------
$debug = (bool) config('app.debug');

error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0'); // Show errors on screen only in debug mode
ini_set('log_errors', '1');                    // Always save errors to the log file

// Make sure the log folder exists
if (!is_dir(LOG_PATH)) {
    @mkdir(LOG_PATH, 0775, true);
}
ini_set('error_log', LOG_PATH . '/php-error.log');

// Use UTF-8 for all string functions
mb_internal_encoding('UTF-8');

// ---------------------------------------------------------------
// Maintenance mode: show the 503 text of src/pages/500.php and stop
// ---------------------------------------------------------------
if (config('app.maintenance')) {
    http_response_code(503);
    $statusCode = 503; // 500.php reads this to pick the maintenance text
    $page = SRC_PATH . '/pages/500.php';
    is_file($page) ? require $page : print('The system is under maintenance.');
    exit;
}

// ---------------------------------------------------------------
// Load the core include files
// A file is loaded only if it exists and is not empty yet.
// ---------------------------------------------------------------
foreach (['error-handlers', 'session-handlers', 'session', 'csrf', 'cors', 'timeout'] as $inc) {
    $file = CORE_PATH . "/includes/{$inc}.php";
    if (is_file($file) && filesize($file) > 0) {
        require_once $file;
    }
}

// ---------------------------------------------------------------
// Load all helper files (core/helpers/*.php)
// Empty files are skipped.
// ---------------------------------------------------------------
foreach (glob(CORE_PATH . '/helpers/*.php') ?: [] as $helper) {
    if (filesize($helper) > 0) {
        require_once $helper;
    }
}

// ---------------------------------------------------------------
// Load the middleware (core/middleware/*.php)
// These only define functions. Nothing runs until a page calls them,
// e.g. requireAuth(). The order matters: routeGuard uses the other two.
// ---------------------------------------------------------------
foreach (['authMiddleware', 'roleMiddleware', 'routeGuard'] as $mw) {
    $file = CORE_PATH . "/middleware/{$mw}.php";
    if (is_file($file) && filesize($file) > 0) {
        require_once $file;
    }
}
