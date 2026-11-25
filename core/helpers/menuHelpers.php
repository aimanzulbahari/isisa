<?php
declare(strict_types=1);

/**
 * Sidebar menu visibility (used by src/layout/sidebar.php and sidebar-mobile.php).
 *
 * Each menu item has a key. menuRoles() says which role codes (ref_role.ref_role_code)
 * may see that key. The same rule is used by the desktop and the mobile menu, so they
 * always show the same items.
 *
 * For now only SUPERADMIN sees the menu (everything). To give another role a menu later,
 * add its code to the list of that key in menuRoles() - nothing else needs to change.
 *
 * NOTE: hiding a menu item is only for looks. A page must still protect itself with
 * requireRole() (core/middleware/roleMiddleware.php).
 */

if (!function_exists('menuRoles')) {
    /**
     * Menu key => role codes that may see it. Keys match the sidebar items.
     *
     * @return array<string,string[]>
     */
    function menuRoles(): array
    {
        return [
            // GUIDE
            'guide'              => ['SUPERADMIN'],
            // UTAMA
            'components'         => ['SUPERADMIN'],
            'dashboard'          => ['SUPERADMIN'],
            // PENGURUSAN SISA
            'waste-food'         => ['SUPERADMIN'],
            'waste-industrial'   => ['SUPERADMIN'],
            'waste-recycling'    => ['SUPERADMIN'],
            'waste-contractor'   => ['SUPERADMIN'],
            // PENGURUSAN UTAMA
            'wisma'              => ['SUPERADMIN'],
            'role'               => ['SUPERADMIN'],
            'users'              => ['SUPERADMIN'],
            // KHIDMAT BANTUAN
            'helpdesk'           => ['SUPERADMIN'],
            // HEBAHAN
            'announcement'       => ['SUPERADMIN'],
            'broadcast-program'  => ['SUPERADMIN'],
            // TETAPAN SISTEM
            'system-audit'       => ['SUPERADMIN'],
            'system-info'        => ['SUPERADMIN'],
            'system-backup'      => ['SUPERADMIN'],
            'system-maintenance' => ['SUPERADMIN'],
            // PELAPORAN
            'reporting'          => ['SUPERADMIN'],
        ];
    }
}

if (!function_exists('canSeeMenu')) {
    /** Can the logged-in user see this menu item? Unknown keys and logged-out users: no. */
    function canSeeMenu(string $key): bool
    {
        $roles = menuRoles()[$key] ?? [];

        return $roles !== [] && hasRole(...$roles);
    }
}

if (!function_exists('canSeeAnyMenu')) {
    /** Can the user see at least one of these keys? (decides if a section title is shown) */
    function canSeeAnyMenu(string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (canSeeMenu($key)) {
                return true;
            }
        }
        return false;
    }
}
