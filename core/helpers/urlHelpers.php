<?php
declare(strict_types=1);

/**
 * URL helpers.
 *
 * This file only needs config(), so bootstrap.php loads it EARLY (before the session
 * and the other helpers). That lets error pages such as 500 and 503 build links
 * even when the system failed half-way through starting up.
 */

if (!function_exists('isHttps')) {
    /**
     * Is the current request using HTTPS?
     */
    function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? '') === '443';
    }
}

if (!function_exists('appUrl')) {
    /**
     * Build a full URL from a path, e.g. appUrl('app/auth/Login/Login.php').
     */
    function appUrl(string $path = ''): string
    {
        return config('app.url') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('routes')) {
    /**
     * The route list from routes/web.php (read once per request).
     *
     * @return array<string,array<string,mixed>> URL path => route settings
     */
    function routes(): array
    {
        static $routes = null;

        if ($routes === null) {
            $file   = ROOT_PATH . '/routes/web.php';
            $loaded = is_file($file) ? require $file : [];
            $routes = is_array($loaded) ? $loaded : []; // An empty file returns 1, not an array
        }
        return $routes;
    }
}

if (!function_exists('routeUrl')) {
    /**
     * Full URL of a route, found by its name in routes/web.php.
     *
     *   routeUrl('login')                     -> http://localhost/isisa/Login
     *   routeUrl('login', ['timeout' => 1])   -> http://localhost/isisa/Login?timeout=1
     *
     * Always use this instead of typing a URL by hand: if a URL changes,
     * you only change it in routes/web.php.
     */
    function routeUrl(string $name, array $query = []): string
    {
        foreach (routes() as $path => $route) {
            if (($route['name'] ?? null) === $name) {
                $url = appUrl($path === '/' ? '' : $path);
                return $query === [] ? $url : $url . '?' . http_build_query($query);
            }
        }
        throw new InvalidArgumentException("Unknown route name: {$name}");
    }
}

if (!function_exists('loginUrl')) {
    /** URL of the login page (the route named "login" in routes/web.php). */
    function loginUrl(): string
    {
        // Error pages and redirects use this, so it must never fail:
        // if the "login" route is missing from web.php, fall back to /Login.
        try {
            return routeUrl('login');
        } catch (InvalidArgumentException) {
            return appUrl('Login');
        }
    }
}
