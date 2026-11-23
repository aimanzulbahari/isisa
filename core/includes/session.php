<?php
declare(strict_types=1);

/**
 * Session setup.
 *
 * Applies secure cookie settings and starts the session.
 * Also provides small helpers for login and logout.
 *
 * Session keys used by the system:
 *   $_SESSION['user_id']        - logged-in user (users.users_id)
 *   $_SESSION['last_activity']  - time of the last request (see timeout.php)
 *   $_SESSION['login_time']     - time the user logged in (see timeout.php)
 *   $_SESSION['csrf_token']     - CSRF token (see csrf.php)
 */

// isHttps() lives in core/helpers/urlHelpers.php (loaded early by bootstrap.php).

if (!function_exists('startSecureSession')) {
    /**
     * Start the session with safe settings (does nothing if already started).
     */
    function startSecureSession(): void
    {
        if (PHP_SAPI === 'cli' || session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $c = config('app.session');

        // Only use cookies for the session ID, never the URL
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_strict_mode', '1'); // Reject session IDs we did not create
        ini_set('session.gc_maxlifetime', (string) $c['lifetime']);

        session_name((string) $c['name']);
        session_set_cookie_params([
            'lifetime' => 0,                           // Cookie ends when the browser closes
            'path'     => '/',
            'secure'   => $c['secure'] || isHttps(),   // Send over HTTPS only when available
            'httponly' => true,                        // JavaScript cannot read the cookie
            'samesite' => (string) $c['samesite'],
        ]);

        session_start();
    }
}

if (!function_exists('loginSession')) {
    /**
     * Mark a user as logged in. Call this after the password is verified.
     * The session ID is renewed to prevent "session fixation" attacks.
     */
    function loginSession(int $userId, array $extra = []): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id']       = $userId;
        $_SESSION['login_time']    = time();
        $_SESSION['last_activity'] = time();

        foreach ($extra as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }
}

if (!function_exists('destroySession')) {
    /**
     * Fully end the session (used for logout and timeout).
     */
    function destroySession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION = [];

        // Remove the cookie from the browser
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 3600,
            'path'     => $p['path'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'],
        ]);

        session_destroy();
    }
}

startSecureSession();
