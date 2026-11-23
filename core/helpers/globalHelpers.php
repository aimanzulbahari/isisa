<?php
declare(strict_types=1);

/**
 * Global helpers: translation, redirects, JSON replies, flash messages,
 * client information and template rendering.
 */

// ---------------------------------------------------------------
// Language / translation
// ---------------------------------------------------------------

if (!function_exists('normalizeLocale')) {
    /**
     * Turn a language code into one the system knows.
     * "BM" (used by the header menu) is the same as "MY".
     */
    function normalizeLocale(?string $code): string
    {
        $code = strtoupper(trim((string) $code));
        if ($code === 'BM') {
            $code = 'MY';
        }
        return array_key_exists($code, config('app.languages')) ? $code : (string) config('app.locale');
    }
}

if (!function_exists('currentLocale')) {
    /**
     * The language the user chose: session first, then cookie, then the .env default.
     */
    function currentLocale(): string
    {
        return normalizeLocale($_SESSION['locale'] ?? $_COOKIE['isisa_locale'] ?? config('app.locale'));
    }
}

if (!function_exists('setLocale')) {
    /**
     * Save the user's language choice (session and a 1-year cookie).
     */
    function setLocale(string $code): void
    {
        $code = normalizeLocale($code);
        $_SESSION['locale'] = $code;

        if (!headers_sent()) {
            setcookie('isisa_locale', $code, [
                'expires'  => time() + 31536000,
                'path'     => '/',
                'secure'   => isHttps(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    }
}

if (!function_exists('loadTranslations')) {
    /**
     * Read the JSON translation file of a language (cached per request).
     *
     * @return array<string,mixed>
     */
    function loadTranslations(string $locale): array
    {
        static $cache = [];

        if (!isset($cache[$locale])) {
            $file  = config('app.languages')[$locale] ?? '';
            $data  = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
            $cache[$locale] = is_array($data) ? $data : [];
        }
        return $cache[$locale];
    }
}

if (!function_exists('__')) {
    /**
     * Translate a key. Supports flat keys ("login.title") and nested JSON.
     * If the key is missing, $default is returned (or the key itself).
     *
     * Example: __('welcome', ['name' => 'Ali'], 'Welcome, :name')
     */
    function __(string $key, array $replace = [], ?string $default = null): string
    {
        $all  = loadTranslations(currentLocale());
        $text = $all[$key] ?? null;

        // Not a flat key: walk down the nested arrays using the dots
        if ($text === null) {
            $node = $all;
            foreach (explode('.', $key) as $part) {
                if (!is_array($node) || !array_key_exists($part, $node)) {
                    $node = null;
                    break;
                }
                $node = $node[$part];
            }
            $text = $node;
        }

        $text = is_string($text) ? $text : ($default ?? $key);

        // Replace :placeholders
        foreach ($replace as $name => $value) {
            $text = str_replace(':' . $name, (string) $value, $text);
        }
        return $text;
    }
}

// ---------------------------------------------------------------
// URLs and responses
// ---------------------------------------------------------------

if (!function_exists('asset')) {
    /**
     * URL of a file inside /public, e.g. asset('assets/css/app.css').
     *
     * With $bustCache = true, "?v=<file time>" is added. Browsers then download
     * the file again whenever it changes, instead of showing an old cached copy.
     */
    function asset(string $path, bool $bustCache = false): string
    {
        $path = ltrim($path, '/');
        $url  = appUrl('public/' . $path);

        $file = PUBLIC_PATH . '/' . $path;
        return $bustCache && is_file($file) ? $url . '?v=' . filemtime($file) : $url;
    }
}

if (!function_exists('resolveLibraries')) {
    /**
     * Used by the library files (css-library, js-library, font-library).
     * Turns a request such as 'dashboard' or ['bootstrap', 'leaflet'] into a list of
     * library names, in the same order as the registry (so load order is always right).
     *
     * @param array<string,mixed>    $registry  All known libraries, in load order.
     * @param array<string,string[]> $profiles  Named groups, e.g. 'auth' => ['bootstrap', ...]
     * @param string|string[]        $request   A profile name, a library name, or a list of both.
     * @return string[]
     */
    function resolveLibraries(array $registry, array $profiles, string|array $request): array
    {
        $wanted = [];

        foreach ((array) $request as $name) {
            if (isset($profiles[$name])) {
                $wanted = array_merge($wanted, $profiles[$name]);
            } elseif (isset($registry[$name])) {
                $wanted[] = $name;
            } else {
                // A typo here is a developer mistake: fail loudly instead of a broken page
                throw new InvalidArgumentException("Unknown library or profile: {$name}");
            }
        }

        return array_values(array_intersect(array_keys($registry), $wanted));
    }
}

if (!function_exists('redirect')) {
    /**
     * Send the user to another page and stop.
     * Only local paths or URLs of this system are allowed (prevents "open redirect").
     */
    function redirect(string $to, int $status = 302): never
    {
        $isExternal = preg_match('#^https?://#i', $to) === 1 && !str_starts_with($to, (string) config('app.url'));
        if ($isExternal || str_starts_with($to, '//')) {
            $to = appUrl(); // Unsafe target: go home instead
        } elseif (!preg_match('#^https?://#i', $to)) {
            $to = appUrl($to);
        }

        header('Location: ' . $to, true, $status);
        exit;
    }
}

if (!function_exists('jsonResponse')) {
    /** Send a JSON answer and stop. */
    function jsonResponse(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('jsonSuccess')) {
    function jsonSuccess(string $message = 'OK', array $data = []): never
    {
        jsonResponse(['success' => true, 'message' => $message, 'data' => $data]);
    }
}

if (!function_exists('jsonError')) {
    function jsonError(string $message, int $status = 400, array $errors = []): never
    {
        jsonResponse(['success' => false, 'message' => $message, 'errors' => $errors], $status);
    }
}

// ---------------------------------------------------------------
// Flash messages (shown once, on the next page)
// ---------------------------------------------------------------

if (!function_exists('setFlash')) {
    /** @param string $type success | error | warning | info */
    function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('pullFlash')) {
    /** Get all flash messages and clear them. */
    function pullFlash(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}

// ---------------------------------------------------------------
// Request / client information
// ---------------------------------------------------------------

if (!function_exists('clientIp')) {
    /**
     * The visitor's IP address.
     * Proxy headers (X-Forwarded-For) are NOT trusted on purpose, because
     * anyone can fake them. Change this only if you run behind a trusted proxy.
     */
    function clientIp(): string
    {
        return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
    }
}

if (!function_exists('userAgent')) {
    function userAgent(): string
    {
        return (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    }
}

if (!function_exists('detectDeviceType')) {
    /** Guess the device type from the user agent: Mobile | Tablet | Desktop | Bot. */
    function detectDeviceType(?string $ua = null): string
    {
        $ua = strtolower($ua ?? userAgent());

        return match (true) {
            $ua === ''                                                  => 'Unknown',
            (bool) preg_match('/bot|crawl|spider|curl|wget/', $ua)      => 'Bot',
            (bool) preg_match('/ipad|tablet|kindle|silk/', $ua)         => 'Tablet',
            (bool) preg_match('/mobi|iphone|android|blackberry/', $ua)  => 'Mobile',
            default                                                     => 'Desktop',
        };
    }
}

if (!function_exists('currentUrl')) {
    function currentUrl(): string
    {
        return (isHttps() ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/');
    }
}

// ---------------------------------------------------------------
// Templates (PDF / Excel / email layouts in src/templates)
// ---------------------------------------------------------------

if (!function_exists('renderTemplate')) {
    /**
     * Run a PHP template file and return its output as a string.
     *
     * Example: renderTemplate('pdf/report.php', ['rows' => $rows])
     * Inside the template, each array key is available as a variable ($rows).
     */
    function renderTemplate(string $template, array $data = []): string
    {
        $file = SRC_PATH . '/templates/' . ltrim($template, '/');

        if (!is_file($file)) {
            throw new RuntimeException("Template not found: {$template}");
        }

        // A closure keeps the template variables away from the global scope
        return (static function () use ($file, $data): string {
            extract($data, EXTR_SKIP);
            ob_start();
            require $file;
            return (string) ob_get_clean();
        })();
    }
}
