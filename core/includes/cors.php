<?php
declare(strict_types=1);

/**
 * CORS (Cross-Origin Resource Sharing) and basic security headers.
 *
 * Allowed websites are listed in CORS_ALLOWED_ORIGINS (.env).
 * When the list is empty, only same-origin requests are allowed
 * (no CORS headers are sent at all).
 */

if (PHP_SAPI !== 'cli' && !headers_sent()) {
    // ---------------------------------------------------------------
    // Basic security headers for every response
    // ---------------------------------------------------------------
    header('X-Content-Type-Options: nosniff');            // Do not guess file types
    header('X-Frame-Options: SAMEORIGIN');                // Block clickjacking
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // ---------------------------------------------------------------
    // CORS: answer only for origins on the allowed list
    // ---------------------------------------------------------------
    $origin  = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = (array) config('app.security.cors_origins');

    if ($origin !== '' && in_array($origin, $allowed, true)) {
        header("Access-Control-Allow-Origin: {$origin}");
        header('Access-Control-Allow-Credentials: true');   // Allow cookies (session)
        header('Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN, X-Requested-With, Accept');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 600');              // Browser may cache the answer for 10 minutes
        header('Vary: Origin');                             // Response depends on the Origin header
    }

    // ---------------------------------------------------------------
    // Preflight: browsers send OPTIONS first. Answer it and stop here.
    // ---------------------------------------------------------------
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(204); // No Content
        exit;
    }
}
