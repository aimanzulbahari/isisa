<?php
declare(strict_types=1);

/**
 * Router: reads routes/web.php and runs the page that matches the URL.
 *
 * index.php (the front controller) calls dispatchRoutes().
 * routes() and routeUrl() are in urlHelpers.php, because links are needed very early.
 */

if (!function_exists('currentRoute')) {
    /**
     * The name of the route that is open now (e.g. "components-basic").
     * dispatchRoutes() sets it. Returns null when the page was not opened through a route.
     *
     * Used to highlight the sidebar menu and to build the breadcrumbs.
     */
    function currentRoute(?string $set = null): ?string
    {
        static $current = null;

        if ($set !== null) {
            $current = $set;
        }
        return $current;
    }
}

if (!function_exists('breadcrumbTrail')) {
    /**
     * The breadcrumb path of a route, from routes/breadcrumbs-route.php.
     *
     * Each step is ['label' => 'Text', 'route' => 'route-name' or null].
     * A step without a route is shown as plain text (it has no page of its own).
     * The LAST step is the current page and is never a link.
     *
     * @return array<int,array{label:string,route:?string}>
     */
    function breadcrumbTrail(?string $route = null): array
    {
        static $trails = null;

        if ($trails === null) {
            $file   = ROOT_PATH . '/routes/breadcrumbs-route.php';
            $loaded = is_file($file) ? require $file : [];
            $trails = is_array($loaded) ? $loaded : []; // An empty file returns 1, not an array
        }
        return $trails[$route ?? currentRoute()] ?? [];
    }
}

if (!function_exists('pageTitle')) {
    /**
     * The title of the page that is open now, for the heading in the header.
     * It is the LAST step of the page's breadcrumb (routes/breadcrumbs-route.php),
     * for example "Jabatan" or "Basic". Returns $default when the route has no breadcrumb.
     *
     * A page can choose its own title by setting $pageTitle before the header is included.
     */
    function pageTitle(string $default = 'Dashboard'): string
    {
        $trail = breadcrumbTrail();
        $last  = $trail !== [] ? end($trail) : null;

        return is_array($last) && !empty($last['label']) ? (string) $last['label'] : $default;
    }
}

if (!function_exists('isInsideAllowedFolder')) {
    /**
     * Is this file inside a folder that may hold pages? (app/ or MyModules/)
     * Pass a full path that was already cleaned with realpath().
     */
    function isInsideAllowedFolder(string $file): bool
    {
        foreach ([APP_PATH, MODULES_PATH] as $folder) {
            $root = realpath($folder);
            if ($root !== false && str_starts_with($file, $root . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('dispatchRoutes')) {
    /**
     * Find the route for the requested URL and run it.
     * Shows the 404 page when no route matches.
     */
    function dispatchRoutes(?string $uri = null, ?string $method = null): void
    {
        $method = strtoupper($method ?? (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $path   = rawurldecode((string) parse_url($uri ?? (string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH));

        // Remove the project folder from the path: "/isisa/Login" -> "/Login"
        $base = (string) parse_url((string) config('app.url'), PHP_URL_PATH);
        if ($base !== '' && ($path === $base || str_starts_with($path, $base . '/'))) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim($path, '/');           // "/Login/" -> "/Login"

        // Refuse strange paths right away
        if (str_contains($path, '..') || str_contains($path, "\0")) {
            respondError(404, 'Page not found.');
        }

        // Find the route. The match ignores upper/lower case.
        $route = null;
        foreach (routes() as $routePath => $candidate) {
            if (strcasecmp($routePath, $path) === 0) {
                $route = $candidate;
                break;
            }
        }
        if ($route === null) {
            respondError(404, 'Page not found.');
        }

        // Remember which route is open (the sidebar and breadcrumbs use this)
        currentRoute($route['name'] ?? null);

        if (!in_array($method, $route['methods'] ?? ['GET', 'POST', 'HEAD'], true)) {
            respondError(405, 'Method not allowed.');
        }

        // Who may open this page? (see core/middleware/routeGuard.php)
        if (!empty($route['guard'])) {
            guard($route['guard']);
        }

        // A function instead of a page
        if (isset($route['handler']) && is_callable($route['handler'])) {
            ($route['handler'])();
            return;
        }

        // A PHP page. Safety check: it must be inside the "app" or "MyModules" folder,
        // so a wrong route can never open other files (like core/ or .env).
        $file = realpath(ROOT_PATH . '/' . ltrim((string) ($route['file'] ?? ''), '/'));
        if ($file === false || !is_file($file) || !isInsideAllowedFolder($file)) {
            respondError(404, 'Page not found.');
        }

        require $file;
    }
}
