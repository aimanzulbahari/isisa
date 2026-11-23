<?php
declare(strict_types=1);

/**
 * Month name helpers (Bahasa Melayu and English).
 */

if (!function_exists('monthNames')) {
    /**
     * All 12 month names for a language.
     *
     * @return array<int,string> Keys are month numbers 1-12.
     */
    function monthNames(?string $locale = null): array
    {
        $names = [
            'MY' => [1 => 'Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'],
            'EN' => [1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        ];
        return $names[normalizeLocale($locale ?? currentLocale())] ?? $names['EN'];
    }
}

if (!function_exists('getMonthName')) {
    /** getMonthName(3) -> "Mac" (MY) or "March" (EN). Short form: "Mac" / "Mar". */
    function getMonthName(int $month, bool $short = false, ?string $locale = null): string
    {
        $name = monthNames($locale)[$month] ?? '';
        return $short ? mb_substr($name, 0, 3) : $name;
    }
}

if (!function_exists('monthOptions')) {
    /**
     * Ready for a <select>: [['value' => 1, 'label' => 'Januari'], ...]
     */
    function monthOptions(?string $locale = null): array
    {
        $options = [];
        foreach (monthNames($locale) as $number => $label) {
            $options[] = ['value' => $number, 'label' => $label];
        }
        return $options;
    }
}

if (!function_exists('yearOptions')) {
    /** List of years for a <select>, newest first. Default: this year back 10 years. */
    function yearOptions(?int $from = null, ?int $to = null): array
    {
        $to   ??= (int) date('Y');
        $from ??= $to - 10;
        return $from <= $to ? range($to, $from) : [];
    }
}
