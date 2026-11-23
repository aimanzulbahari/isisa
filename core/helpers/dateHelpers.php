<?php
declare(strict_types=1);

/**
 * Date and time helpers.
 * The database stores dates as "Y-m-d" and times as "Y-m-d H:i:s".
 */

if (!function_exists('parseDate')) {
    /** Turn text into a DateTimeImmutable, or null if it is empty/invalid. */
    function parseDate(?string $value): ?DateTimeImmutable
    {
        if (isBlank($value) || str_starts_with((string) $value, '0000-00-00')) {
            return null;
        }
        try {
            return new DateTimeImmutable((string) $value);
        } catch (Exception) {
            return null;
        }
    }
}

if (!function_exists('formatDate')) {
    /** "2026-11-14" -> "14/11/2026". Returns "-" when empty. */
    function formatDate(?string $value, string $format = 'd/m/Y'): string
    {
        $date = parseDate($value);
        return $date ? $date->format($format) : '-';
    }
}

if (!function_exists('formatDateTime')) {
    /** "2026-11-14 15:30:00" -> "14/11/2026 03:30 PM" */
    function formatDateTime(?string $value, string $format = 'd/m/Y h:i A'): string
    {
        return formatDate($value, $format);
    }
}

if (!function_exists('formatDateLong')) {
    /** "2026-11-14" -> "14 November 2026" (month name follows the chosen language). */
    function formatDateLong(?string $value): string
    {
        $date = parseDate($value);
        return $date ? $date->format('j') . ' ' . getMonthName((int) $date->format('n')) . ' ' . $date->format('Y') : '-';
    }
}

if (!function_exists('toDbDate')) {
    /**
     * Turn a date from a form into the database format.
     * Accepts "14/11/2026" or "14-11-2026" (day first) and "2026-11-14".
     * Returns null if it is not a real date.
     */
    function toDbDate(?string $value): ?string
    {
        $value = trim((string) $value);

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
            $date = DateTimeImmutable::createFromFormat('!' . $format, $value);
            $errors = DateTimeImmutable::getLastErrors();
            // The format check makes sure "31/02/2026" is rejected
            if ($date && (!$errors || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date->format('Y-m-d');
            }
        }
        return null;
    }
}

if (!function_exists('isValidDate')) {
    function isValidDate(?string $value): bool
    {
        return toDbDate($value) !== null;
    }
}

if (!function_exists('timeAgo')) {
    /** "5 minutes ago" / "5 minit lepas", in the chosen language. */
    function timeAgo(?string $value): string
    {
        $date = parseDate($value);
        if (!$date) {
            return '-';
        }

        $seconds = time() - $date->getTimestamp();
        if ($seconds < 0) {
            return formatDateTime($value);
        }

        $my    = normalizeLocale(currentLocale()) === 'MY';
        $units = [
            [31536000, $my ? 'tahun' : 'year'],
            [2592000,  $my ? 'bulan' : 'month'],
            [86400,    $my ? 'hari' : 'day'],
            [3600,     $my ? 'jam' : 'hour'],
            [60,       $my ? 'minit' : 'minute'],
        ];

        foreach ($units as [$size, $label]) {
            if ($seconds >= $size) {
                $count  = intdiv($seconds, $size);
                $plural = (!$my && $count > 1) ? 's' : '';
                return $my ? "{$count} {$label} lepas" : "{$count} {$label}{$plural} ago";
            }
        }
        return $my ? 'Baru sahaja' : 'Just now';
    }
}
