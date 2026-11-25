<?php
declare(strict_types=1);

/**
 * CSS library loader.
 *
 * Prints <link> tags for the third-party CSS files inside public/UI.
 * Put it inside <head>:
 *
 *   <?php require_once PUBLIC_PATH . '/library/css-library.php'; ?>
 *   <?= cssLibrary('dashboard') ?>
 *
 * What to ask for:
 *   cssLibrary('auth')                       // a profile (a ready-made group)
 *   cssLibrary('dashboard', 'leaflet')       // a profile plus extra libraries
 *   cssLibrary(['bootstrap', 'toastr'])      // pick libraries one by one
 *
 * Profiles:
 *   auth        : bootstrap, bootstrap-icons, sweetalert2
 *   error       : bootstrap, bootstrap-icons
 *   dashboard   : bootstrap, bootstrap-icons, select2, sweetalert2, toastr
 *
 * Extra libraries (not in a profile): leaflet (maps), fullcalendar (calendar)
 *
 * Load your own CSS AFTER this, so your styles can override the libraries.
 * Fonts are loaded separately by font-library.php.
 */

if (!function_exists('cssLibraryRegistry')) {
    /**
     * Every CSS library and its file. The ORDER here is the load order.
     * Always use the .min.css files (smaller and faster).
     */
    function cssLibraryRegistry(): array
    {
        return [
            'bootstrap'       => 'UI/bootstrap/css/bootstrap.min.css',
            'bootstrap-icons' => 'UI/bootstrap-icon/bootstrap-icons.min.css',
            'select2'         => 'UI/select2/css/select2.min.css',
            'sweetalert2'     => 'UI/sweetalert2/sweetalert2.min.css',
            'toastr'          => 'UI/toastr/toastr.min.css',
            'leaflet'         => 'UI/leaflet/leaflet.css',
            // FullCalendar v7: base styles, then the Monarch theme in green (matches the ISISA colour)
            'fullcalendar'    => [
                'UI/fullcalendar/skeleton.css',
                'UI/fullcalendar/themes/monarch/theme.css',
                'UI/fullcalendar/themes/monarch/palettes/green.css',
            ],
        ];
    }
}

if (!function_exists('cssLibraryProfiles')) {
    /** Ready-made groups for each type of page. */
    function cssLibraryProfiles(): array
    {
        return [
            'auth'      => ['bootstrap', 'bootstrap-icons', 'sweetalert2'], // sweetalert2: popup messages (e.g. wrong password)
            'error'     => ['bootstrap', 'bootstrap-icons'],
            'dashboard' => ['bootstrap', 'bootstrap-icons', 'select2', 'sweetalert2', 'toastr'],
        ];
    }
}

if (!function_exists('cssLibrary')) {
    /**
     * Build the <link> tags.
     *
     * @param string|string[] $libs  A profile, a library name, or a list of both.
     * @param string|string[] $extra More libraries to add (same format as $libs).
     */
    function cssLibrary(string|array $libs = 'dashboard', string|array $extra = []): string
    {
        $registry = cssLibraryRegistry();
        $names    = resolveLibraries(
            $registry,
            cssLibraryProfiles(),
            array_merge((array) $libs, (array) $extra)
        );

        $html = '';
        foreach ($names as $name) {
            // One file, or a list for a library that needs several (e.g. fullcalendar)
            foreach ((array) $registry[$name] as $file) {
                if (!is_file(PUBLIC_PATH . '/' . $file)) {
                    throw new RuntimeException("CSS library file is missing: public/{$file}");
                }
                $html .= '<link rel="stylesheet" href="' . e(asset($file, true)) . '">' . PHP_EOL;
            }
        }
        return $html;
    }
}
