<?php
declare(strict_types=1);

/**
 * Route guard: one short call that picks the right middleware.
 *
 * Put it at the top of a page:
 *
 *   guard('guest');                   // login / register pages
 *   guard('auth');                    // any logged-in user
 *   guard('role:ADMIN,SUPERADMIN');   // only these roles
 *
 * It can also protect a whole group of pages from one place, for example in routes/web.php:
 *
 *   guardRoutes($_SERVER['REQUEST_URI'], [
 *       '/app/auth/' => 'guest',
 *       '/app/admin' => 'role:ADMIN,SUPERADMIN',
 *       '/'          => 'auth',
 *   ]);
 */

if (!function_exists('guard')) {
    /**
     * Apply one access rule.
     *
     * @param string $rule guest | auth | role:CODE1,CODE2 | public (no check)
     */
    function guard(string $rule): void
    {
        $rule = trim($rule);

        if ($rule === 'public') {
            return;
        }
        if ($rule === 'guest') {
            requireGuest();
            return;
        }
        if ($rule === 'auth') {
            requireAuth();
            return;
        }
        if (str_starts_with($rule, 'role:')) {
            $codes = array_filter(array_map('trim', explode(',', substr($rule, 5))));
            requireRole(...$codes);
            return;
        }

        // A typo in a rule must never leave a page open by accident
        throw new InvalidArgumentException("Unknown guard rule: {$rule}");
    }
}

if (!function_exists('guardRoutes')) {
    /**
     * Pick the rule by the start of the URL path. The FIRST matching prefix wins,
     * so list the most specific paths first. If nothing matches, the request is
     * allowed through (add a '/' => 'auth' line at the end to deny by default).
     *
     * @param array<string,string> $rules path prefix => rule
     */
    function guardRoutes(string $uri, array $rules): void
    {
        $path = (string) parse_url($uri, PHP_URL_PATH);

        // Remove the project folder (e.g. "/isisa") so rules can start from "/app/..."
        $base = (string) parse_url((string) config('app.url'), PHP_URL_PATH);
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        foreach ($rules as $prefix => $rule) {
            if (str_starts_with($path, $prefix)) {
                guard($rule);
                return;
            }
        }
    }
}
