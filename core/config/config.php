<?php
declare(strict_types=1);

/**
 * Core configuration.
 *
 * This file does three things:
 *   1. Defines the project path constants.
 *   2. Loads Composer and the .env file.
 *   3. Provides the env() and config() helper functions.
 */

// ---------------------------------------------------------------
// Path constants (always use these instead of hard-coded paths)
// ---------------------------------------------------------------
defined('ROOT_PATH')    || define('ROOT_PATH', dirname(__DIR__, 2));
defined('CORE_PATH')    || define('CORE_PATH', ROOT_PATH . '/core');
defined('APP_PATH')     || define('APP_PATH', ROOT_PATH . '/app');
defined('MODULES_PATH') || define('MODULES_PATH', ROOT_PATH . '/MyModules'); // Pages of the system modules
defined('SRC_PATH')     || define('SRC_PATH', ROOT_PATH . '/src');
defined('PUBLIC_PATH')  || define('PUBLIC_PATH', ROOT_PATH . '/public');
defined('STORAGE_PATH') || define('STORAGE_PATH', ROOT_PATH . '/storage');
defined('LOG_PATH')     || define('LOG_PATH', STORAGE_PATH . '/logs');
defined('UPLOAD_PATH')  || define('UPLOAD_PATH', STORAGE_PATH . '/uploads');
defined('TEMP_PATH')    || define('TEMP_PATH', STORAGE_PATH . '/temp');

// ---------------------------------------------------------------
// Composer autoloader
// ---------------------------------------------------------------
$autoload = ROOT_PATH . '/vendor/autoload.php';
if (!is_file($autoload)) {
    http_response_code(500);
    exit('Dependencies are missing. Please run "composer install".');
}
require_once $autoload;

// ---------------------------------------------------------------
// Load the .env file (skipped quietly if the file does not exist)
// ---------------------------------------------------------------
if (is_file(ROOT_PATH . '/.env')) {
    Dotenv\Dotenv::createImmutable(ROOT_PATH)->safeLoad();
}

if (!function_exists('env')) {
    /**
     * Read a value from the environment (.env).
     * The text "true", "false" and "null" are converted to real PHP values.
     *
     * Example: env('APP_DEBUG', false)
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        // Key not found: use the default value
        if ($value === false || $value === null) {
            return $default;
        }
        if (!is_string($value)) {
            return $value;
        }

        // Convert text values to real PHP types
        return match (strtolower(trim($value))) {
            'true'  => true,
            'false' => false,
            'null'  => null,
            default => $value,
        };
    }
}

if (!function_exists('config')) {
    /**
     * Read a config value using dot notation.
     * The first part is the file name inside core/config/.
     *
     * Example: config('app.name') reads the "name" key from app.php
     */
    function config(string $key, mixed $default = null): mixed
    {
        static $loaded = []; // Cache, so each file is only loaded once

        $parts = explode('.', $key);
        $file  = array_shift($parts);

        if (!isset($loaded[$file])) {
            $path = CORE_PATH . "/config/{$file}.php";
            $loaded[$file] = is_file($path) ? (require $path) : [];
        }

        // Walk down the array, one key at a time
        $value = $loaded[$file];
        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }
}
