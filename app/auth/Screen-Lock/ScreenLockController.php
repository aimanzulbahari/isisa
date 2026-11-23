<?php
declare(strict_types=1);

/**
 * ScreenLockController: the logic of the Lock-Screen page (app/auth/Screen-Lock/Screen-Lock.php).
 *
 * Screen-Lock.php calls handleScreenLock() once. The function:
 *   - GET  (the user clicked "Kunci Skrin"): locks the screen, remembers the page the user was on,
 *          and returns the message to show (if any).
 *   - POST (the user typed the password): checks it. Correct -> unlock and go back to the page the
 *          user was on. Wrong -> back to the lock page with a message (Post/Redirect/Get).
 *
 * While the screen is locked, requireAuth() (core/middleware/authMiddleware.php) sends every other
 * page to the lock page. The user is still logged in, so the session is kept.
 *
 * Wrong passwords are written to login_logs, so they count towards the same lockout as the
 * login page (nobody can guess the password here without limit).
 * CSRF is already checked for every POST by core/includes/csrf.php.
 */

require_once __DIR__ . '/ScreenLockModel.php';

if (!function_exists('rememberPageBeforeLock')) {
    /**
     * Remember the page the user was on (from the Referer header), so unlocking brings them back to it.
     * Only pages of this website are accepted. Otherwise the user goes to the dashboard after unlocking.
     */
    function rememberPageBeforeLock(): void
    {
        $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
        $from    = parse_url($referer);
        $site    = parse_url((string) config('app.url'));

        if ($from === false || empty($from['host']) || empty($site['host'])) {
            return;
        }
        // Same website only (host and port)
        if (strcasecmp($from['host'], $site['host']) !== 0 || ($from['port'] ?? null) !== ($site['port'] ?? null)) {
            return;
        }

        $path = (string) ($from['path'] ?? '/');
        $uri  = $path . (isset($from['query']) ? '?' . $from['query'] : '');

        // Not the lock page itself
        if (strcasecmp(rtrim($path, '/'), rtrim((string) parse_url(routeUrl('lock-screen'), PHP_URL_PATH), '/')) === 0) {
            return;
        }
        $_SESSION['intended_url'] = $uri; // checked again by pullIntendedUrl() before it is used
    }
}

if (!function_exists('screenLockFail')) {
    /** Keep the message in the session and redirect back to the lock page (Post/Redirect/Get). */
    function screenLockFail(string $message): never
    {
        $_SESSION['lock_flash'] = ['type' => 'danger', 'message' => $message];

        redirect(routeUrl('lock-screen'), 303);
    }
}

if (!function_exists('handleScreenLock')) {
    /**
     * Run the lock page logic.
     *
     * @return array{type:string,message:string,name:string}
     *         type/message are '' when there is nothing to show. name is the user's name for the page.
     */
    function handleScreenLock(): array
    {
        $user   = requireAuth();
        $userId = (int) $user['users_id'];
        $myID   = (string) $user['myID'];
        $view   = ['type' => '', 'message' => '', 'name' => userDisplayName()];

        // ---------- Normal page view: lock the screen and show the form ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            if (!isScreenLocked()) {
                lockScreen();
                rememberPageBeforeLock();
                audit('SCREEN_LOCK', 'users', $userId, null, ['myID' => $myID]);
            }

            // A wrong password that redirected here: show it once, then forget it
            $flash = $_SESSION['lock_flash'] ?? null;
            unset($_SESSION['lock_flash']);
            if (is_array($flash)) {
                $view['type']    = (string) ($flash['type'] ?? 'danger');
                $view['message'] = (string) ($flash['message'] ?? '');
            }
            return $view;
        }

        // ---------- Form submitted: unlock ----------
        if (!isScreenLocked()) {
            redirect(dashboardUrl()); // nothing to unlock
        }

        $password = (string) ($_POST['password'] ?? ''); // never trim a password
        if ($password === '') {
            screenLockFail('Sila masukkan kata laluan.');
        }

        // 1. Too many wrong passwords (the same limit as the login page)?
        if (isLoginLocked($myID)) {
            recordLoginLog($userId, $myID, 'LOCKED', 'Too many failed attempts (screen lock)');
            audit('SCREEN_UNLOCK_LOCKED', 'users', $userId, null, ['myID' => $myID]);
            $minutesLeft = loginLockMinutesLeft($myID) ?: (int) config('app.security.lockout_minutes');
            screenLockFail(
                'Terlalu banyak percubaan gagal. Akaun dikunci selama ' . $minutesLeft
                . ' minit. Sila cuba lagi kemudian.'
            );
        }

        // 2. Check the password of the logged-in user
        $hash = ScreenLockModel::passwordHash($userId);
        if (strlen($password) > 255 || $hash === null || !verifyPassword($password, $hash)) {
            recordLoginLog($userId, $myID, 'FAILED', 'Wrong password (screen lock)');
            audit('SCREEN_UNLOCK_FAILED', 'users', $userId, null, ['myID' => $myID]);

            $left    = loginAttemptsLeft($myID);
            $message = 'Kata laluan tidak betul.';
            $message .= $left > 0
                ? " Baki percubaan: {$left}."
                : ' Akaun dikunci selama ' . (int) config('app.security.lockout_minutes') . ' minit.';
            screenLockFail($message);
        }

        // 3. Correct password: unlock and go back to the page the user was on
        unlockScreen();
        audit('SCREEN_UNLOCK', 'users', $userId, null, ['myID' => $myID]);

        redirect(pullIntendedUrl());
    }
}
