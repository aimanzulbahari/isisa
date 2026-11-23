<?php
declare(strict_types=1);

/**
 * CSRF protection.
 *
 * A secret token is stored in the session. Every request that changes data
 * (POST, PUT, PATCH, DELETE) must send the same token back, otherwise it is
 * rejected. This stops other websites from submitting forms on the user's behalf.
 *
 * In an HTML form:
 *   <?= csrfField() ?>
 *
 * In AJAX, send the header (or put <?= csrfMeta() ?> in <head> and read it):
 *   X-CSRF-TOKEN: <token>
 *
 * The check runs automatically at the bottom of this file.
 */

if (!function_exists('csrfToken')) {
    /**
     * Get the current token. A new one is created when needed or when expired.
     */
    function csrfToken(): string
    {
        $lifetime = (int) config('app.security.csrf_lifetime');
        $expired  = isset($_SESSION['csrf_time']) && (time() - $_SESSION['csrf_time']) > $lifetime;

        if (empty($_SESSION['csrf_token']) || $expired) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_time']  = time();
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrfField')) {
    /** Hidden input for HTML forms. */
    function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('csrfMeta')) {
    /** Meta tag so JavaScript (AJAX) can read the token. */
    function csrfMeta(): string
    {
        return '<meta name="csrf-token" content="'
            . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('csrfVerify')) {
    /**
     * Check the token sent by the browser (form field or header).
     */
    function csrfVerify(): bool
    {
        $sent = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $real = $_SESSION['csrf_token'] ?? '';

        if ($sent === '' || $real === '') {
            return false;
        }

        // The token must not be expired
        $lifetime = (int) config('app.security.csrf_lifetime');
        if ((time() - ($_SESSION['csrf_time'] ?? 0)) > $lifetime) {
            return false;
        }

        // hash_equals compares in constant time (safe against timing attacks)
        return hash_equals($real, (string) $sent);
    }
}

// ---------------------------------------------------------------
// Automatic check for every request that changes data
// ---------------------------------------------------------------
if (
    PHP_SAPI !== 'cli'
    && in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['POST', 'PUT', 'PATCH', 'DELETE'], true)
    && !csrfVerify()
) {
    error_log('[CSRF] Invalid token from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . ' on ' . ($_SERVER['REQUEST_URI'] ?? ''));
    // Status 400, but the page shows the "session expired" text of 400.php (key 419).
    // A real 419 status cannot be used: Apache does not know that code and turns it into 500.
    respondError(400, 'Your session has expired or the request is not valid. Please refresh the page and try again.', 419);
}
