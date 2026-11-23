<?php
declare(strict_types=1);

/**
 * Authentication middleware.
 *
 * Put one call at the top of a page or API file:
 *
 *   requireAuth();    // only logged-in users may continue
 *   requireGuest();   // only logged-OUT users (login, register, forgot password pages)
 *
 * If the check fails, the request stops here. Page requests are redirected,
 * AJAX / API requests get a JSON answer (401).
 */

if (!function_exists('dashboardUrl')) {
    /** Where a logged-in user goes by default. */
    function dashboardUrl(): string
    {
        return routeUrl('dashboard');
    }
}

if (!function_exists('rememberIntendedUrl')) {
    /**
     * Remember the page the visitor wanted, so we can send them back after login.
     * Only normal page views (GET) are remembered.
     */
    function rememberIntendedUrl(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && !isJsonRequest()) {
            $_SESSION['intended_url'] = (string) ($_SERVER['REQUEST_URI'] ?? '');
        }
    }
}

if (!function_exists('pullIntendedUrl')) {
    /**
     * Get (and forget) the remembered page. Use it right after a successful login:
     *   redirect(pullIntendedUrl());
     * Only paths on this website are accepted. Anything else falls back to the dashboard.
     */
    function pullIntendedUrl(): string
    {
        $path = (string) ($_SESSION['intended_url'] ?? '');
        unset($_SESSION['intended_url']);

        // Must start with a single "/" (blocks "//evil.com" and full URLs)
        if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//') || str_contains($path, '\\')) {
            return dashboardUrl();
        }

        $origin = parse_url((string) config('app.url'));
        return ($origin['scheme'] ?? 'http') . '://' . ($origin['host'] ?? 'localhost')
            . (isset($origin['port']) ? ':' . $origin['port'] : '') . $path;
    }
}

if (!function_exists('denyAccess')) {
    /**
     * Stop a guest: redirect to login (pages) or answer 401 (AJAX / API).
     */
    function denyAccess(string $reason = ''): never
    {
        if (isJsonRequest()) {
            respondError(401, 'Please log in to continue.');
        }

        rememberIntendedUrl();
        redirect(loginUrl() . ($reason !== '' ? '?reason=' . rawurlencode($reason) : ''));
    }
}

if (!function_exists('requireAuth')) {
    /**
     * Allow only logged-in users with an ACTIVE account.
     *
     * The account is checked in the database on every request, so a user who was
     * deactivated or locked by an admin is logged out immediately.
     *
     * @return array<string,mixed> The current user (same as currentUser()).
     */
    function requireAuth(): array
    {
        if (!isLoggedIn()) {
            denyAccess();
        }

        $user = currentUser();

        // User was deleted, or the account is no longer active
        if ($user === null || (int) $user['ref_status_id'] !== config('app.status')['ACTIVE']) {
            recordLogout();
            destroySession();

            if (isJsonRequest()) {
                respondError(401, 'Your account is not active.');
            }
            redirect(loginUrl() . '?reason=inactive');
        }

        // System is under maintenance and this role is not allowed in: log the user out and show the 503 page.
        // Logout stays open, so a blocked user can always leave.
        if (currentRoute() !== 'logout') {
            $blocked = maintenanceBlocksRole((string) $user['ref_role_code']);
            if ($blocked !== null) {
                recordLogout();
                destroySession();
                respondError(503, maintenanceNotice($blocked));
            }
        }

        // Screen is locked: only the Lock-Screen page (to unlock) and Logout are allowed
        if (isScreenLocked() && !in_array(currentRoute(), ['lock-screen', 'logout'], true)) {
            if (isJsonRequest()) {
                respondError(401, 'The screen is locked. Please unlock it first.');
            }
            redirect(routeUrl('lock-screen'));
        }

        return $user;
    }
}

if (!function_exists('requireGuest')) {
    /**
     * Allow only visitors who are NOT logged in.
     * A logged-in user is sent to the dashboard.
     */
    function requireGuest(): void
    {
        if (isLoggedIn()) {
            if (isJsonRequest()) {
                respondError(403, 'You are already logged in.');
            }
            redirect(dashboardUrl());
        }
    }
}
