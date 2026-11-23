<?php
declare(strict_types=1);

/**
 * Role helpers.
 *
 * Role codes come from ref_role: SUPERADMIN, ADMIN, USERS, GUEST.
 * Always compare by CODE (not by ID), so the logic keeps working if IDs change.
 */

if (!function_exists('currentRole')) {
    /** Role code of the logged-in user, e.g. "ADMIN". Null when logged out. */
    function currentRole(): ?string
    {
        return currentUser()['ref_role_code'] ?? null;
    }
}

if (!function_exists('hasRole')) {
    /**
     * Does the user have one of these roles?
     * hasRole('ADMIN', 'SUPERADMIN')
     */
    function hasRole(string ...$codes): bool
    {
        $role = currentRole();
        return $role !== null && in_array($role, array_map('strtoupper', $codes), true);
    }
}

if (!function_exists('isSuperadmin')) {
    function isSuperadmin(): bool
    {
        return hasRole('SUPERADMIN');
    }
}

if (!function_exists('isAdmin')) {
    /** Admin or Superadmin. */
    function isAdmin(): bool
    {
        return hasRole('SUPERADMIN', 'ADMIN');
    }
}

if (!function_exists('getRoles')) {
    /**
     * Active roles for a dropdown.
     * The Superadmin role is hidden unless the current user is a Superadmin.
     *
     * @return array<int,array<string,mixed>>
     */
    function getRoles(): array
    {
        $sql = 'SELECT ref_role_id, ref_role_code, ref_role_description
                FROM ref_role
                WHERE ref_status_id = :active';
        if (!isSuperadmin()) {
            $sql .= " AND ref_role_code <> 'SUPERADMIN'";
        }

        $stmt = db()->prepare($sql . ' ORDER BY ref_role_id');
        $stmt->execute(['active' => config('app.status')['ACTIVE']]);
        return $stmt->fetchAll();
    }
}
