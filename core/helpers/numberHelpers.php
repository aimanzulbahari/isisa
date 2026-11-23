<?php
declare(strict_types=1);

/**
 * Number helpers.
 */

if (!function_exists('formatNumber')) {
    /** 1234567.8 -> "1,234,567.80" */
    function formatNumber(int|float|string|null $number, int $decimals = 2): string
    {
        return number_format((float) $number, $decimals, '.', ',');
    }
}

if (!function_exists('formatCurrency')) {
    /** 1234.5 -> "RM 1,234.50" */
    function formatCurrency(int|float|string|null $amount, string $symbol = 'RM'): string
    {
        return $symbol . ' ' . formatNumber($amount, 2);
    }
}

if (!function_exists('formatPercent')) {
    /** formatPercent(25, 200) -> "12.5%"  (part / total) */
    function formatPercent(int|float $part, int|float $total, int $decimals = 1): string
    {
        return $total == 0 ? '0%' : round(($part / $total) * 100, $decimals) . '%';
    }
}

if (!function_exists('formatBytes')) {
    /** 1536 -> "1.5 KB" */
    function formatBytes(int $bytes, int $decimals = 1): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        $units = ['KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units));
        return round($bytes / (1024 ** $power), $decimals) . ' ' . $units[$power - 1];
    }
}

if (!function_exists('toFloat')) {
    /** Read a number typed by a user: "RM 1,234.50" -> 1234.5 */
    function toFloat(mixed $value): float
    {
        return (float) preg_replace('/[^0-9.\-]/', '', (string) $value);
    }
}

if (!function_exists('toInt')) {
    /** Returns the integer, or $default when the value is not a whole number. */
    function toInt(mixed $value, int $default = 0): int
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int) $value : $default;
    }
}
