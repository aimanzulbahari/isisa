<?php
declare(strict_types=1);

/**
 * Authentication helpers: passwords, current user, login logs and lockout.
 *
 * Related tables: users, login_logs (see database/migrations).
 * In login_logs, `username_attempted` stores the IC number (myID) typed by the user.
 */

// ---------------------------------------------------------------
// Passwords
// ---------------------------------------------------------------

if (!function_exists('hashPassword')) {
    /** Hash a password for the database. Never store the plain password. */
    function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

if (!function_exists('verifyPassword')) {
    function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}

if (!function_exists('passwordNeedsRehash')) {
    /** True when the stored hash uses an old algorithm. Re-save the hash after a successful login. */
    function passwordNeedsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }
}

// ---------------------------------------------------------------
// Current user
// ---------------------------------------------------------------

if (!function_exists('currentUserId')) {
    function currentUserId(): ?int
    {
        return isLoggedIn() ? (int) $_SESSION['user_id'] : null;
    }
}

if (!function_exists('currentUser')) {
    /**
     * The logged-in user's record, with role and status text. The password is NOT included.
     * The result is cached for the request. Use $refresh = true after changing the user.
     *
     * @return array<string,mixed>|null null when nobody is logged in.
     */
    function currentUser(bool $refresh = false): ?array
    {
        static $user = null;

        $id = currentUserId();
        if ($id === null) {
            return null;
        }
        if ($user !== null && !$refresh && (int) $user['users_id'] === $id) {
            return $user;
        }

        $stmt = db()->prepare(
            'SELECT u.users_id, u.myID, u.fullname, u.dob, u.age, u.email, u.phone_number,
                    u.ref_role_id, r.ref_role_code, r.ref_role_description,
                    u.ref_role_type_id, u.ref_department_id, u.ref_branch_id,
                    u.ref_division_id, u.ref_section_id, u.position,
                    u.ref_status_id, s.ref_status_code
             FROM users u
             JOIN ref_role r   ON r.ref_role_id = u.ref_role_id
             JOIN ref_status s ON s.ref_status_id = u.ref_status_id
             WHERE u.users_id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch() ?: null;

        return $user;
    }
}

if (!function_exists('userDisplayName')) {
    /**
     * The name to show on screen for the logged-in user.
     * A name typed all in CAPITALS (or all in lowercase), like on a MyKad, is tidied up:
     * "AHMAD FAIZ BIN ALI" -> "Ahmad Faiz bin Ali".
     * A name that already has mixed upper/lower case is shown exactly as saved
     * ("Superadmin ISISA", "John McDonald"), because changing it could damage it.
     * Returns $fallback when nobody is logged in.
     * (The result is plain text. Escape it with e() when you print it.)
     */
    function userDisplayName(string $fallback = 'Pengguna'): string
    {
        $name = trim((string) (currentUser()['fullname'] ?? ''));
        if ($name === '') {
            return $fallback;
        }
        $looksUnformatted = $name === mb_strtoupper($name) || $name === mb_strtolower($name);
        return $looksUnformatted ? properName($name) : $name;
    }
}

if (!function_exists('userInitials')) {
    /** "Ahmad Faiz bin Ali" -> "AF" (for the avatar circle). */
    function userInitials(?string $fullname = null): string
    {
        $fullname ??= (string) (currentUser()['fullname'] ?? '');
        $words = preg_split('/\s+/', trim($fullname)) ?: [];
        $out   = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $out .= mb_strtoupper(mb_substr($word, 0, 1));
        }
        return $out !== '' ? $out : '?';
    }
}

// ---------------------------------------------------------------
// Screen lock (the user stays logged in, but must type the password again)
// ---------------------------------------------------------------

if (!function_exists('isScreenLocked')) {
    /** True when the logged-in user locked the screen and has not unlocked it yet. */
    function isScreenLocked(): bool
    {
        return isLoggedIn() && !empty($_SESSION['screen_locked']);
    }
}

if (!function_exists('lockScreen')) {
    /** Lock the screen. While locked, requireAuth() sends every page to the Lock-Screen page. */
    function lockScreen(): void
    {
        $_SESSION['screen_locked'] = true;
    }
}

if (!function_exists('unlockScreen')) {
    function unlockScreen(): void
    {
        unset($_SESSION['screen_locked']);
    }
}

// ---------------------------------------------------------------
// Login logs and lockout
// ---------------------------------------------------------------

if (!function_exists('recordLoginLog')) {
    /**
     * Write one row to login_logs.
     *
     * @param int|null $userId   null when the IC number does not exist.
     * @param string   $myID     The IC number that was typed.
     * @param string   $status   SUCCESS or FAILED (a key of config('app.status')).
     * @param string|null $reason Why it failed, e.g. "Wrong password".
     */
    function recordLoginLog(?int $userId, string $myID, string $status, ?string $reason = null): void
    {
        try {
            $stmt = db()->prepare(
                'INSERT INTO login_logs
                    (user_id, username_attempted, ref_status_id, failure_reason, ip_address, user_agent, device_type, session_id)
                 VALUES
                    (:user_id, :username, :status, :reason, :ip, :agent, :device, :session)'
            );
            $stmt->execute([
                'user_id'  => $userId,
                'username' => substr($myID, 0, 150),
                'status'   => config('app.status')[$status],
                'reason'   => $reason,
                'ip'       => clientIp(),
                'agent'    => userAgent() ?: null,
                'device'   => detectDeviceType(),
                'session'  => session_id() ?: null,
            ]);
        } catch (Throwable $e) {
            // A logging problem must never block the login itself
            error_log('[AUTH] Could not write login log: ' . $e->getMessage());
        }
    }
}

if (!function_exists('recordLogout')) {
    /** Fill logout_at for the current session. Call it before destroySession(). */
    function recordLogout(): void
    {
        if (!session_id()) {
            return;
        }
        try {
            db()->prepare('UPDATE login_logs SET logout_at = NOW() WHERE session_id = :sid AND logout_at IS NULL')
                ->execute(['sid' => session_id()]);
        } catch (Throwable $e) {
            error_log('[AUTH] Could not write logout time: ' . $e->getMessage());
        }
    }
}

if (!function_exists('failedLoginCount')) {
    /**
     * Number of wrong-password attempts for an IC number inside the lockout window,
     * counted after the last successful login.
     */
    function failedLoginCount(string $myID): int
    {
        $minutes = (int) config('app.security.lockout_minutes');
        $status  = config('app.status');

        $stmt = db()->prepare(
            "SELECT COUNT(*) FROM login_logs
             WHERE username_attempted = :u1
               AND ref_status_id = :failed
               AND login_at > DATE_SUB(NOW(), INTERVAL {$minutes} MINUTE)
               AND login_at > COALESCE(
                    (SELECT MAX(login_at) FROM login_logs WHERE username_attempted = :u2 AND ref_status_id = :success),
                    '1970-01-01')"
        );
        $stmt->execute([
            'u1' => $myID, 'u2' => $myID,
            'failed' => $status['FAILED'], 'success' => $status['SUCCESS'],
        ]);
        return (int) $stmt->fetchColumn();
    }
}

if (!function_exists('isLoginLocked')) {
    /** True when there were too many wrong attempts recently. */
    function isLoginLocked(string $myID): bool
    {
        return failedLoginCount($myID) >= (int) config('app.security.max_attempts');
    }
}

if (!function_exists('loginLockMinutesLeft')) {
    /**
     * Minutes left until a locked account can try again (rounded UP, never less than 1).
     * Returns 0 when the account is not locked.
     *
     * An account is locked while the failed attempts inside the lockout window are
     * max_attempts or more. It opens again when the newest "max_attempts"-th failure
     * becomes older than the window. So the unlock time is:
     *     (time of the max_attempts-th most recent failure) + lockout_minutes
     * Failures made while locked are logged as LOCKED, not FAILED, so they never extend the lock.
     *
     * The time is worked out inside MySQL (NOW() and login_at use the same time zone).
     */
    function loginLockMinutesLeft(string $myID): int
    {
        $minutes = (int) config('app.security.lockout_minutes');
        $max     = max(1, (int) config('app.security.max_attempts'));
        $offset  = $max - 1;
        $status  = config('app.status');

        $stmt = db()->prepare(
            "SELECT TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(login_at, INTERVAL {$minutes} MINUTE))
             FROM login_logs
             WHERE username_attempted = :u1
               AND ref_status_id = :failed
               AND login_at > DATE_SUB(NOW(), INTERVAL {$minutes} MINUTE)
               AND login_at > COALESCE(
                    (SELECT MAX(login_at) FROM login_logs WHERE username_attempted = :u2 AND ref_status_id = :success),
                    '1970-01-01')
             ORDER BY login_at DESC
             LIMIT 1 OFFSET {$offset}"
        );
        $stmt->execute([
            'u1' => $myID, 'u2' => $myID,
            'failed' => $status['FAILED'], 'success' => $status['SUCCESS'],
        ]);
        $seconds = $stmt->fetchColumn();

        // false = fewer than max_attempts failures = not locked
        return $seconds === false ? 0 : max(1, (int) ceil(((int) $seconds) / 60));
    }
}

if (!function_exists('loginAttemptsLeft')) {
    function loginAttemptsLeft(string $myID): int
    {
        return max(0, (int) config('app.security.max_attempts') - failedLoginCount($myID));
    }
}

// ---------------------------------------------------------------
// Password rules
// ---------------------------------------------------------------

if (!function_exists('validatePasswordStrength')) {
    /**
     * Check a new password.
     *
     * @return string[] List of problems. Empty = the password is good.
     */
    function validatePasswordStrength(string $password): array
    {
        $errors = [];

        if (mb_strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must have an uppercase letter.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must have a lowercase letter.';
        }
        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Password must have a number.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must have a symbol (e.g. @ # $ !).';
        }
        return $errors;
    }
}
