<?php
declare(strict_types=1);

/**
 * Database configuration and connection (MySQL / MariaDB, using PDO).
 *
 * Usage:
 *   $pdo = db();                    // get the database connection
 *   config('database.host');        // read a single setting
 */

if (!function_exists('db')) {
    /**
     * Get the shared PDO connection.
     * The connection is created once and reused for the whole request.
     */
    function db(): PDO
    {
        static $pdo = null;

        // Already connected: reuse it
        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $c   = config('database');
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $c['host'],
            $c['port'],
            $c['database'],
            $c['charset']
        );

        try {
            $pdo = new PDO($dsn, $c['username'], $c['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw errors as exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return rows as arrays
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements (safer)
                // Set the charset and make the database use the same timezone as PHP
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$c['charset']} COLLATE {$c['collation']}, time_zone = '" . date('P') . "'",
            ]);
        } catch (PDOException $e) {
            // Always write the real error to the log
            error_log('[DB] ' . $e->getMessage());
            http_response_code(503);

            // Only show the real error in debug mode, so secrets are never leaked
            exit(config('app.debug') ? 'DB Error: ' . $e->getMessage() : 'Service unavailable.');
        }

        return $pdo;
    }
}

// Database settings (read from .env)
return [
    'host'      => env('DB_HOST', '127.0.0.1'),
    'port'      => (int) env('DB_PORT', 3306),
    'database'  => env('DB_DATABASE', 'isisa_db'),
    'username'  => env('DB_USERNAME', 'root'),
    'password'  => (string) env('DB_PASSWORD', ''),
    'charset'   => env('DB_CHARSET', 'utf8mb4'),
    'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
];
