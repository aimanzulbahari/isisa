<?php
declare(strict_types=1);

/**
 * LoginController: the logic of the login page (app/auth/Login/Login.php).
 *
 * Login.php calls handleLogin() once. The function:
 *   - on a normal page view: returns the message to show (e.g. "session expired",
 *     or the reason the last login failed)
 *   - on a form submit (POST): validates, checks lockout, verifies the password,
 *     logs the attempt, then ALWAYS redirects (Post/Redirect/Get):
 *       success -> the dashboard (or the page the user wanted)
 *       failure -> back to /Login, which shows the message once (see loginFail())
 *
 * CSRF is already checked for every POST by core/includes/csrf.php.
 */

require_once __DIR__ . '/LoginModel.php';

if (!function_exists('loginNoticeFromQuery')) {
    /**
     * Message for the reasons the user was sent here (?timeout=... / ?reason=...).
     *
     * @return array{type:string,message:string}|null
     */
    function loginNoticeFromQuery(): ?array
    {
        $timeout = (string) ($_GET['timeout'] ?? '');
        $reason  = (string) ($_GET['reason'] ?? '');

        if ($timeout === 'idle') {
            return ['type' => 'warning', 'message' => 'Sesi anda tamat kerana tidak aktif. Sila log masuk semula.'];
        }
        if ($timeout === 'expired') {
            return ['type' => 'warning', 'message' => 'Sesi anda telah tamat tempoh. Sila log masuk semula.'];
        }
        if ($reason === 'inactive') {
            return ['type' => 'danger', 'message' => 'Akaun anda tidak aktif. Sila hubungi pentadbir sistem.'];
        }
        if ($reason !== '') {
            return ['type' => 'info', 'message' => 'Sila log masuk untuk meneruskan.'];
        }
        return null;
    }
}

if (!function_exists('loginAudit')) {
    /**
     * Write the login event to the audits table.
     * Only for IC numbers that belong to a user (audits.auditable_id is required).
     * Unknown IC numbers are still recorded in login_logs.
     */
    function loginAudit(string $event, int $userId, string $noKp, ?string $reason = null): void
    {
        audit($event, 'users', $userId, null, array_filter([
            'myID'   => $noKp,
            'reason' => $reason,
        ], static fn ($v) => $v !== null));
    }
}

if (!function_exists('loginFail')) {
    /**
     * Login failed: keep the message (and the IC number typed) in the session and
     * redirect back to the login page (Post/Redirect/Get).
     *
     * Why redirect: if the page were shown straight after the POST, pressing Refresh
     * would make the browser ask "Resubmit the form?" and send the login attempt again.
     * After the redirect, the page the user sees is a normal GET page, so Refresh is safe.
     * The message is shown ONCE (handleLogin() removes it from the session).
     */
    function loginFail(string $message, string $noKp, ?array $maintenance = null): never
    {
        // $maintenance (see maintenanceDetails()) makes the login page show the maintenance popup
        $_SESSION['login_flash'] = ['type' => $maintenance !== null ? 'warning' : 'danger', 'message' => $message, 'old_no_kp' => $noKp, 'maintenance' => $maintenance];

        redirect(routeUrl('login'), 303); // 303 = "See Other": the right code to answer a POST with a GET
    }
}

if (!function_exists('handleLogin')) {
    /**
     * Run the login page logic.
     *
     * @return array{type:string,message:string,old_no_kp:string,maintenance:?array}
     *         type/message are '' when there is nothing to show. old_no_kp refills the IC field.
     *         maintenance is set when login was refused because of maintenance (see maintenanceDetails()).
     */
    function handleLogin(): array
    {
        $view = ['type' => '', 'message' => '', 'old_no_kp' => '', 'maintenance' => null];

        // ---------- Normal page view ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            // A message waiting for the login page: a failed login (see loginFail()) or "you logged out"
            // (see handleLogout()). Read it once, then forget it.
            $failed = $_SESSION['login_flash'] ?? null;
            unset($_SESSION['login_flash']);

            // System under maintenance right now: every visitor sees the maintenance popup.
            // This is checked BEFORE the waiting message, so "you logged out" no longer hides it.
            $current = maintenanceCurrent();
            if ($current !== null) {
                pullFlash();
                return [
                    'type'        => 'warning',
                    'message'     => maintenanceMessage($current),
                    'old_no_kp'   => is_array($failed) ? (string) ($failed['old_no_kp'] ?? '') : '',
                    'maintenance' => maintenanceDetails($current),
                ];
            }

            if (is_array($failed)) {
                return [
                    'type'      => (string) ($failed['type'] ?? 'danger'),
                    'message'   => (string) ($failed['message'] ?? ''),
                    'old_no_kp' => (string) ($failed['old_no_kp'] ?? ''),
                    'maintenance' => is_array($failed['maintenance'] ?? null) ? $failed['maintenance'] : null,
                ];
            }

            $notice = loginNoticeFromQuery();
            if ($notice === null) {
                // Flash messages set by other pages (e.g. "Password changed")
                $flash  = pullFlash();
                $notice = $flash[0] ?? null;
            }
            return $notice !== null
                ? ['type' => $notice['type'] === 'error' ? 'danger' : $notice['type'], 'message' => $notice['message'], 'old_no_kp' => '', 'maintenance' => null]
                : $view;
        }

        // ---------- Form submitted ----------
        $noKp     = cleanMyID((string) ($_POST['no_kp'] ?? ''));   // digits only, no dashes
        $password = (string) ($_POST['password'] ?? '');          // never trim a password

        // 1. Input check
        if ($noKp === '' || $password === '') {
            return loginFail('Sila isi No. Kad Pengenalan dan kata laluan.', $noKp);
        }
        if (!isValidMyID($noKp)) {
            return loginFail('No. Kad Pengenalan tidak sah. Masukkan 12 digit tanpa sengkang (-).', $noKp);
        }
        if (strlen($password) > 255) {
            return loginFail('No. Kad Pengenalan atau kata laluan tidak betul.', $noKp);
        }

        // 2. Locked after too many wrong passwords?
        if (isLoginLocked($noKp)) {
            $user = LoginModel::findByMyID($noKp);
            recordLoginLog($user !== null ? (int) $user['users_id'] : null, $noKp, 'LOCKED', 'Too many failed attempts');
            if ($user !== null) {
                loginAudit('LOGIN_LOCKED', (int) $user['users_id'], $noKp, 'Too many failed attempts');
            }
            // Show the minutes that are LEFT (e.g. 12), not the full lockout time (15)
            $minutesLeft = loginLockMinutesLeft($noKp) ?: (int) config('app.security.lockout_minutes');
            return loginFail(
                'Terlalu banyak percubaan gagal. Akaun dikunci selama ' . $minutesLeft
                . ' minit. Sila cuba lagi kemudian.',
                $noKp
            );
        }

        // 3. Find the user and check the password
        $user = LoginModel::findByMyID($noKp);

        if ($user === null) {
            // Still run a hash check so the response time does not reveal that the IC number does not exist
            verifyPassword($password, '$2y$10$9swb2JJ0zp2YoPiwD6ea1eh0bQvD.xA4SpbKLzXLeaRJyJrtw9.US');
            recordLoginLog(null, $noKp, 'FAILED', 'IC number not found');
            return loginFail('No. Kad Pengenalan atau kata laluan tidak betul.', $noKp);
        }

        $userId = (int) $user['users_id'];

        if (!verifyPassword($password, (string) $user['password'])) {
            recordLoginLog($userId, $noKp, 'FAILED', 'Wrong password');
            loginAudit('LOGIN_FAILED', $userId, $noKp, 'Wrong password');

            $left    = loginAttemptsLeft($noKp);
            $message = 'No. Kad Pengenalan atau kata laluan tidak betul.';
            $message .= $left > 0
                ? " Baki percubaan: {$left}."
                : ' Akaun dikunci selama ' . (int) config('app.security.lockout_minutes') . ' minit.';
            return loginFail($message, $noKp);
        }

        // 4. Password is correct. Only ACTIVE accounts may log in.
        //    (Checked after the password so strangers cannot learn which accounts exist.)
        if ((int) $user['ref_status_id'] !== config('app.status')['ACTIVE']) {
            recordLoginLog($userId, $noKp, 'FAILED', 'Account not active');
            loginAudit('LOGIN_FAILED', $userId, $noKp, 'Account not active');
            return loginFail('Akaun anda tidak aktif. Sila hubungi pentadbir sistem.', $noKp);
        }

        // 5. Maintenance: a role that is not allowed cannot log in while the system is under maintenance
        //    (checked after the password, so strangers cannot learn which accounts exist)
        $blocked = maintenanceBlocksRole((string) $user['ref_role_code']);
        if ($blocked !== null) {
            recordLoginLog($userId, $noKp, 'FAILED', 'Maintenance mode');
            loginAudit('LOGIN_FAILED', $userId, $noKp, 'Maintenance mode');
            return loginFail(maintenanceMessage($blocked), $noKp, maintenanceDetails($blocked));
        }

        // 6. Success
        if (passwordNeedsRehash((string) $user['password'])) {
            LoginModel::updatePasswordHash($userId, hashPassword($password));
        }

        loginSession($userId);                       // new session ID + user_id in the session
        recordLoginLog($userId, $noKp, 'SUCCESS');   // after loginSession so the new session ID is saved
        loginAudit('LOGIN', $userId, $noKp);

        redirect(pullIntendedUrl());                 // the page the user wanted, or the dashboard
    }
}
