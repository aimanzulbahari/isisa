<?php
declare(strict_types=1);

/**
 * Font library loader.
 *
 * Loads the local font files from public/assets/fonts, so the system does not
 * need Google Fonts (works offline and does not send visitors' data to Google).
 * Put it inside <head>, BEFORE the CSS files:
 *
 *   <?php require_once PUBLIC_PATH . '/library/font-library.php'; ?>
 *   <?= fontLibrary('auth') ?>
 *
 * Which one to use:
 *   fontLibrary('auth')       // Plus Jakarta Sans - for login pages (Jakarta folder)
 *   fontLibrary('error')      // Plus Jakarta Sans - for error pages (Jakarta folder)
 *   fontLibrary('dashboard')  // Inter             - for the dashboard (Inter folder)
 *
 * The font names match the CSS variables of each page:
 *   auth-css / error-css : 'Plus Jakarta Sans'
 *   dashboard-css        : 'Inter'
 *
 * Variable fonts are used: ONE file covers every weight from 200 to 800.
 */

if (!function_exists('fontLibraryRegistry')) {
    /**
     * Each font: its CSS name, folder inside assets/fonts and its files.
     * 'normal' and 'italic' are the variable font files.
     */
    function fontLibraryRegistry(): array
    {
        return [
            'jakarta' => [
                'family' => 'Plus Jakarta Sans',
                'folder' => 'assets/fonts/Jakarta',
                'normal' => 'PlusJakartaSans-VariableFont_wght.ttf',
                'italic' => 'PlusJakartaSans-Italic-VariableFont_wght.ttf',
                'weight' => '200 800', // Lightest and heaviest weight in the file
            ],
            'inter' => [
                'family' => 'Inter',
                'folder' => 'assets/fonts/Inter',
                // Inter variable font (axes: opsz + wght). The static/ folder is not used by this loader.
                'normal' => 'Inter-VariableFont_opsz,wght.ttf',
                'italic' => 'Inter-Italic-VariableFont_opsz,wght.ttf',
                'weight' => '100 900', // Lightest and heaviest weight in the file
            ],
        ];
    }
}

if (!function_exists('fontLibraryProfiles')) {
    /** Which font each type of page uses. */
    function fontLibraryProfiles(): array
    {
        return [
            'auth'      => ['jakarta'],
            'error'     => ['jakarta'],
            'dashboard' => ['inter'],
        ];
    }
}

if (!function_exists('fontLibrary')) {
    /**
     * Build the font tags: a preload hint (so the font starts downloading early)
     * and the @font-face rules.
     *
     * @param string|string[] $fonts A profile (auth / error / dashboard) or font name(s) (jakarta / inter).
     */
    function fontLibrary(string|array $fonts = 'dashboard'): string
    {
        $registry = fontLibraryRegistry();
        $names    = resolveLibraries($registry, fontLibraryProfiles(), $fonts);

        $preload = '';
        $css     = '';

        foreach ($names as $name) {
            $font = $registry[$name];

            foreach (['normal', 'italic'] as $style) {
                $path = $font['folder'] . '/' . $font[$style];
                if (!is_file(PUBLIC_PATH . '/' . $path)) {
                    throw new RuntimeException("Font file is missing: public/{$path}");
                }
                $url = e(asset($path, true));

                // Only the upright style is preloaded: italic is rare and would waste bandwidth
                if ($style === 'normal') {
                    $preload .= '<link rel="preload" href="' . $url . '" as="font" type="font/ttf" crossorigin>' . PHP_EOL;
                }

                // font-display: swap = show text at once with a fallback font, then switch
                $css .= "@font-face{font-family:'{$font['family']}';font-style:{$style};font-weight:{$font['weight']};"
                      . "font-display:swap;src:url('{$url}') format('truetype');}" . PHP_EOL;
            }
        }

        return $preload . '<style>' . PHP_EOL . $css . '</style>' . PHP_EOL;
    }
}
