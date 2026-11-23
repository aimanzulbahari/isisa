<?php
declare(strict_types=1);

/**
 * String helpers.
 */

if (!function_exists('e')) {
    /** Escape text for safe output in HTML (prevents XSS). Use it on ALL user data. */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('isBlank')) {
    /** True for null, "" or text that has only spaces. The number 0 is NOT blank. */
    function isBlank(mixed $value): bool
    {
        return $value === null || (is_string($value) && trim($value) === '');
    }
}

if (!function_exists('strLimit')) {
    /** Cut long text and add "..." at the end. */
    function strLimit(string $text, int $limit = 100, string $end = '...'): string
    {
        return mb_strlen($text) <= $limit ? $text : rtrim(mb_substr($text, 0, $limit)) . $end;
    }
}

if (!function_exists('slugify')) {
    /** "Laporan Bulanan 2026" -> "laporan-bulanan-2026" */
    function slugify(string $text): string
    {
        $text = strtolower(trim((string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text)));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        return trim($text, '-');
    }
}

if (!function_exists('properName')) {
    /**
     * Format a person's name: "AHMAD FAIZ BIN ALI" -> "Ahmad Faiz bin Ali".
     * Malay link words (bin, binti, a/l, a/p) stay in lowercase.
     */
    function properName(string $name): string
    {
        $lower = ['bin', 'binti', 'bt', 'b', 'a/l', 'a/p', 'al', 'ap', 'van', 'de'];
        $words = preg_split('/\s+/', mb_strtolower(trim($name))) ?: [];

        foreach ($words as $i => $word) {
            if ($i > 0 && in_array($word, $lower, true)) {
                continue;
            }
            // Also capitalise after "-" and "'" (e.g. "Nur-Aina", "D'Souza")
            $words[$i] = preg_replace_callback(
                '/(^|[-\'])(\p{L})/u',
                static fn(array $m) => $m[1] . mb_strtoupper($m[2]),
                $word
            );
        }
        return implode(' ', $words);
    }
}

if (!function_exists('maskString')) {
    /**
     * Hide the middle of a text, e.g. an IC number.
     * maskString('900101105544', 3, 3) -> "900******544"
     */
    function maskString(string $text, int $showStart = 3, int $showEnd = 3, string $mask = '*'): string
    {
        $len = mb_strlen($text);
        if ($len <= $showStart + $showEnd) {
            return str_repeat($mask, $len);
        }
        return mb_substr($text, 0, $showStart)
            . str_repeat($mask, $len - $showStart - $showEnd)
            . ($showEnd > 0 ? mb_substr($text, -$showEnd) : '');
    }
}

if (!function_exists('maskEmail')) {
    /** "ahmad.faiz@mbsa.gov.my" -> "ah*******@mbsa.gov.my" */
    function maskEmail(string $email): string
    {
        [$user, $domain] = array_pad(explode('@', $email, 2), 2, '');
        return $domain === '' ? maskString($email) : maskString($user, 2, 0) . '@' . $domain;
    }
}

if (!function_exists('randomString')) {
    /** Random letters and numbers (safe to use for tokens and codes). */
    function randomString(int $length = 32): string
    {
        return substr(bin2hex(random_bytes((int) ceil($length / 2))), 0, $length);
    }
}

if (!function_exists('sanitizeText')) {
    /**
     * Clean text from a form: trim spaces, remove control characters and tags.
     * Escaping for HTML is still done with e() when the text is printed.
     */
    function sanitizeText(?string $text): string
    {
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string) $text) ?? '';
        return trim(strip_tags($text));
    }
}
