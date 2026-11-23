<?php
declare(strict_types=1);

/**
 * Global error handling.
 *
 * Catches PHP warnings, uncaught exceptions and fatal errors, then:
 *   - writes the full details to storage/logs/php-error.log
 *   - shows a safe, simple message to the user (details only in debug mode)
 *   - answers with JSON for AJAX / API requests, and HTML for normal pages
 */

if (!function_exists('isJsonRequest')) {
    /**
     * Does the client expect a JSON answer? (AJAX call or API route)
     */
    function isJsonRequest(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $with   = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        $uri    = $_SERVER['REQUEST_URI'] ?? '';

        return strcasecmp($with, 'XMLHttpRequest') === 0
            || str_contains($accept, 'application/json')
            || str_contains($uri, '/api/');
    }
}

if (!function_exists('errorPageFor')) {
    /**
     * Which page in src/pages shows this HTTP status?
     *   5xx (500, 503...) -> 500.php
     *   everything else   -> 400.php (400, 401, 403, 404, 419...)
     * Each page has its own text for every code it supports.
     */
    function errorPageFor(int $status): string
    {
        return $status >= 500 ? '500' : '400';
    }
}

if (!function_exists('respondError')) {
    /**
     * Stop the request and send an error response in the right format.
     */
    function respondError(int $status, string $message, ?int $textCode = null): never
    {
        // Cannot change headers if output has already started
        if (!headers_sent()) {
            http_response_code($status);
        }

        if (isJsonRequest()) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Show one of our own error pages (src/pages). These variables are
        // visible inside the page: $statusCode and $errorMessage.
        $page = SRC_PATH . '/pages/' . errorPageFor($status) . '.php';
        if (is_file($page) && filesize($page) > 0) {
            $statusCode    = $status;
            $errorTextCode = $textCode; // Optional: use the text of ANOTHER code (see CSRF in csrf.php)
            $errorMessage = $message; // The page decides if it is safe to show
            require $page;
            exit;
        }

        $safe = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        echo "<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"UTF-8\"><title>Error {$status}</title></head>"
           . "<body style=\"font-family:Arial,sans-serif;text-align:center;padding:60px;\">"
           . "<h1>{$status}</h1><p>{$safe}</p></body></html>";
        exit;
    }
}

// ---------------------------------------------------------------
// 1. PHP errors (warnings, notices...) are turned into exceptions,
//    so every problem goes through the same handler below.
// ---------------------------------------------------------------
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    // Ignore errors that are turned off in error_reporting (e.g. the @ operator)
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// ---------------------------------------------------------------
// 2. Uncaught exceptions
// ---------------------------------------------------------------
set_exception_handler(static function (Throwable $e): void {
    error_log(sprintf(
        '[ERROR] %s: %s in %s:%d%s%s',
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        PHP_EOL,
        $e->getTraceAsString()
    ));

    $message = config('app.debug')
        ? get_class($e) . ': ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')'
        : 'Something went wrong. Please try again later.';

    respondError(500, $message);
});

// ---------------------------------------------------------------
// 3. Fatal errors (these skip the handlers above, so we check at shutdown)
// ---------------------------------------------------------------
register_shutdown_function(static function (): void {
    $error = error_get_last();
    $fatal = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];

    if ($error !== null && in_array($error['type'], $fatal, true)) {
        error_log("[FATAL] {$error['message']} in {$error['file']}:{$error['line']}");

        // Drop any half-written page before showing the error
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        respondError(500, 'Something went wrong. Please try again later.');
    }
});
