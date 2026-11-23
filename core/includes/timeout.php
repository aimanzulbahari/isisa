<?php
declare(strict_types=1);

/**
 * Session timeout.
 *
 * Logs the user out when:
 *   - idle too long      (SESSION_IDLE_TIMEOUT, no requests for a while)
 *   - session too old    (SESSION_LIFETIME, counted from the login time)
 *
 * Only logged-in users are checked. Guests are never timed out.
 */

// appUrl() and loginUrl() live in core/helpers/urlHelpers.php
// (bootstrap.php loads that file before this one).

if (!function_exists('checkSessionTimeout')) {
    /**
     * Check the timers. Returns "idle", "expired", or null when still valid.
     */
    function checkSessionTimeout(): ?string
    {
        if (!isLoggedIn()) {
            return null;
        }

        $now  = time();
        $idle = (int) config('app.session.idle_timeout');
        $life = (int) config('app.session.lifetime');

        if (($now - ($_SESSION['last_activity'] ?? $now)) > $idle) {
            return 'idle';
        }
        if (($now - ($_SESSION['login_time'] ?? $now)) > $life) {
            return 'expired';
        }

        return null;
    }
}

if (PHP_SAPI !== 'cli') {
    $reason = checkSessionTimeout();

    if ($reason !== null) {
        destroySession();

        // AJAX / API: send 401 so the page can redirect by itself
        if (isJsonRequest()) {
            respondError(401, 'Your session has ended. Please log in again.');
        }

        // Normal page: go back to the login page with the reason
        header('Location: ' . loginUrl() . '?timeout=' . $reason);
        exit;
    }

    // Still valid: restart the idle timer.
    // Requests that only check the session (e.g. a keep-alive ping) can set
    // $skipActivityUpdate = true before including bootstrap.php.
    if (isLoggedIn() && empty($skipActivityUpdate)) {
        $_SESSION['last_activity'] = time();
    }
}
