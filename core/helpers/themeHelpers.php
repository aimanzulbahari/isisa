<?php
declare(strict_types=1);

/**
 * Theme colour helpers.
 * The colours match the swatches in src/layout/header.php.
 * The choice is saved in a cookie, so it works before login too.
 */

if (!function_exists('themeColors')) {
    /** Theme name => main colour. */
    function themeColors(): array
    {
        return [
            'green'  => '#386641',
            'blue'   => '#2f6db5',
            'purple' => '#6b4fbb',
            'orange' => '#d4781f',
            'red'    => '#c0392b',
            'teal'   => '#1f8a8a',
            'pink'   => '#b83280',
            'dark'   => '#374151',
        ];
    }
}

if (!function_exists('currentTheme')) {
    /** The chosen theme name. Falls back to "green". */
    function currentTheme(): string
    {
        $theme = strtolower((string) ($_COOKIE['isisa_theme'] ?? 'green'));
        return array_key_exists($theme, themeColors()) ? $theme : 'green';
    }
}

if (!function_exists('setTheme')) {
    /** Save the theme choice for 1 year. Unknown names are ignored. */
    function setTheme(string $theme): bool
    {
        $theme = strtolower($theme);
        if (!array_key_exists($theme, themeColors()) || headers_sent()) {
            return false;
        }

        $_COOKIE['isisa_theme'] = $theme;
        return setcookie('isisa_theme', $theme, [
            'expires'  => time() + 31536000,
            'path'     => '/',
            'secure'   => isHttps(),
            'httponly' => false, // JavaScript may read it to switch the colour instantly
            'samesite' => 'Lax',
        ]);
    }
}

if (!function_exists('themeColor')) {
    /** Main colour (hex) of the current theme. */
    function themeColor(): string
    {
        return themeColors()[currentTheme()];
    }
}

if (!function_exists('themeStyleTag')) {
    /**
     * A <style> tag that sets the CSS variable --theme-color.
     * Put it inside <head>: <?= themeStyleTag() ?>
     */
    function themeStyleTag(): string
    {
        return '<style>:root{--theme-color:' . themeColor() . ';}</style>';
    }
}
