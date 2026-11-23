<?php
declare(strict_types=1);

/**
 * Role middleware.
 *
 * Use it at the top of a page or API file, after login is required:
 *
 *   requireRole('ADMIN', 'SUPERADMIN');   // any of these roles
 *   requireAdmin();                       // Admin or Superadmin
 *   requireSuperadmin();                  // Superadmin only
 *
 * Each function also checks that the user is logged in (requireAuth),
 * so you do not need to call both.
 * A user with the wrong role gets a 403 "Forbidden" answer.
 */

if (!function_exists('requireRole')) {
    /**
     * Allow only users whose role is one of the given role codes.
     *
     * @return array<string,mixed> The current user.
     */
    function requireRole(string ...$codes): array
    {
        $user = requireAuth();

        if (!hasRole(...$codes)) {
            // Keep a trace in the log: this may be someone trying to reach a page they should not
            error_log(sprintf(
                '[ACCESS] Denied: user %d (%s) tried %s. Needs: %s',
                $user['users_id'],
                $user['ref_role_code'],
                $_SERVER['REQUEST_URI'] ?? '',
                implode(',', $codes)
            ));
            respondError(403, 'You do not have permission to access this page.');
        }

        return $user;
    }
}

if (!function_exists('requireAdmin')) {
    /** Admin or Superadmin. */
    function requireAdmin(): array
    {
        return requireRole('SUPERADMIN', 'ADMIN');
    }
}

if (!function_exists('requireSuperadmin')) {
    function requireSuperadmin(): array
    {
        return requireRole('SUPERADMIN');
    }
}
