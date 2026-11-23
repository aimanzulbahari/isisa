<?php
declare(strict_types=1);

/**
 * Application settings.
 *
 * Values come from the .env file. Read them with config(), for example:
 *   config('app.name')
 *   config('app.session.lifetime')
 */
return [
    // --- General ---
    'name'        => env('APP_NAME', 'ISISA'),
    'env'         => env('APP_ENV', 'production'),       // local | staging | production
    'debug'       => (bool) env('APP_DEBUG', false),     // Show detailed errors (keep OFF in production)
    'url'         => rtrim((string) env('APP_URL', 'http://localhost/isisa'), '/'),
    'timezone'    => env('APP_TIMEZONE', 'Asia/Kuala_Lumpur'),
    'locale'      => strtoupper((string) env('APP_LOCALE', 'MY')), // Default language
    'maintenance' => (bool) env('APP_MAINTENANCE', false),         // true = show the 503 page

    // --- Languages: language code => translation file ---
    'languages' => [
        'MY' => CORE_PATH . '/lang/MY/my.json',
        'EN' => CORE_PATH . '/lang/EN/en.json',
    ],

    // --- Session (the .env values are in minutes, converted to seconds here) ---
    'session' => [
        'name'         => env('SESSION_NAME', 'isisa_session'),
        'lifetime'     => (int) env('SESSION_LIFETIME', 120) * 60,    // Maximum session age
        'idle_timeout' => (int) env('SESSION_IDLE_TIMEOUT', 30) * 60, // Auto logout when inactive
        'secure'       => (bool) env('SESSION_SECURE_COOKIE', false), // Send cookie over HTTPS only
        'samesite'     => env('SESSION_SAMESITE', 'Lax'),
        'table'        => 'sessions', // Database table used to store sessions
    ],

    // --- Security ---
    'security' => [
        'csrf_lifetime'   => (int) env('CSRF_TOKEN_LIFETIME', 120) * 60, // How long a CSRF token is valid
        // Turn "a.com,b.com" into a clean list, and ignore empty values
        'cors_origins'    => array_values(array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))))),
        'max_attempts'    => (int) env('LOGIN_MAX_ATTEMPTS', 5),    // Wrong passwords allowed before lock
        'lockout_minutes' => (int) env('LOGIN_LOCKOUT_MINUTES', 15), // How long the account stays locked
    ],

    // --- Email (SMTP, used by PHPMailer) ---
    'mail' => [
        'host'       => env('MAIL_HOST', ''),
        'port'       => (int) env('MAIL_PORT', 587),
        'username'   => env('MAIL_USERNAME', ''),
        'password'   => env('MAIL_PASSWORD', ''),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'from'       => env('MAIL_FROM_ADDRESS', ''),
        'from_name'  => env('MAIL_FROM_NAME', 'ISISA'),
    ],

    // --- File uploads ---
    'upload' => [
        'max_size' => (int) env('UPLOAD_MAX_SIZE_MB', 10) * 1024 * 1024, // Converted to bytes
        'path'     => UPLOAD_PATH,
    ],

    // --- Logging ---
    'log' => [
        'level' => env('LOG_LEVEL', 'debug'),
        'path'  => LOG_PATH,
    ],

    // --- Status IDs ---
    // These must match the rows in database/migrations/ref_status.sql
    'status' => [
        'ACTIVE'     => 1,
        'NOT_ACTIVE' => 2,
        'SUCCESS'    => 3,
        'FAILED'     => 4,
        'LOCKED'     => 5,
        // Helpdesk ticket statuses (helpdesk.status_id)
        'TICKET_OPEN'        => 6,
        'TICKET_IN_PROGRESS' => 7,
        'TICKET_ON_HOLD'     => 8,
        'TICKET_TRANSFERRED' => 9,
        'TICKET_CLOSED'      => 10,
    ],
];
