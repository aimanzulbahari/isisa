<?php
declare(strict_types=1);

/**
 * JavaScript library loader.
 *
 * Prints <script> tags for the third-party JS files inside public/UI.
 * Put it at the END of <body>, before your own scripts:
 *
 *   <?php require_once PUBLIC_PATH . '/library/js-library.php'; ?>
 *   <?= jsLibrary('dashboard') ?>
 *   <script type="module" src="<?= asset('assets/js/dashboard-js/main.js', true) ?>"></script>
 *
 * What to ask for:
 *   jsLibrary('auth')                       // a profile (a ready-made group)
 *   jsLibrary('dashboard', 'leaflet')       // a profile plus extra libraries
 *   jsLibrary(['jquery', 'toastr'])         // pick libraries one by one
 *
 * Profiles:
 *   auth        : bootstrap, sweetalert2
 *   error       : bootstrap
 *   dashboard   : jquery, bootstrap, axios, chart, select2, sweetalert2, toastr
 *
 * Extra libraries (not in a profile): leaflet (maps), gsap (animations, used by the home page)
 *
 * Dependencies are handled for you: the load order below is fixed, so jQuery always
 * comes before Select2 and Toastr. If you pick select2 or toastr, jquery is added.
 */

if (!function_exists('jsLibraryRegistry')) {
    /**
     * Every JS library and its file. The ORDER here is the load order.
     * Always use the .min.js files (smaller and faster).
     */
    function jsLibraryRegistry(): array
    {
        return [
            'jquery'      => 'UI/jquery/jquery.min.js',
            'bootstrap'   => 'UI/bootstrap/js/bootstrap.bundle.min.js', // "bundle" already contains Popper
            'axios'       => 'UI/axios/axios.min.js',
            'chart'       => 'UI/chartJS/chart.umd.min.js',
            'gsap'        => 'UI/gsap/gsap.min.js',          // Animations (home page)
            'select2'     => 'UI/select2/js/select2.full.min.js',
            'sweetalert2' => 'UI/sweetalert2/sweetalert2.min.js',       // Its CSS comes from css-library.php
            'toastr'      => 'UI/toastr/toastr.min.js',
            'leaflet'     => 'UI/leaflet/leaflet.js',
        ];
    }
}

if (!function_exists('jsLibraryProfiles')) {
    /** Ready-made groups for each type of page. */
    function jsLibraryProfiles(): array
    {
        return [
            'auth'      => ['bootstrap', 'sweetalert2'], // sweetalert2: popup messages (e.g. wrong password)
            'error'     => ['bootstrap'],
            'dashboard' => ['jquery', 'bootstrap', 'axios', 'chart', 'select2', 'sweetalert2', 'toastr'],
        ];
    }
}

if (!function_exists('jsLibraryDependencies')) {
    /** Libraries that cannot work alone: name => libraries it needs. */
    function jsLibraryDependencies(): array
    {
        return [
            'select2' => ['jquery'],
            'toastr'  => ['jquery'],
        ];
    }
}

if (!function_exists('select2LanguageFile')) {
    /** Select2 text (e.g. "No results found") in the user's language: Malay or English. */
    function select2LanguageFile(): string
    {
        return currentLocale() === 'MY' ? 'UI/select2/js/i18n/ms.js' : 'UI/select2/js/i18n/en.js';
    }
}

if (!function_exists('jsLibrary')) {
    /**
     * Build the <script> tags.
     *
     * @param string|string[] $libs  A profile, a library name, or a list of both.
     * @param string|string[] $extra More libraries to add (same format as $libs).
     */
    function jsLibrary(string|array $libs = 'dashboard', string|array $extra = []): string
    {
        $registry = jsLibraryRegistry();
        $request  = array_merge((array) $libs, (array) $extra);

        // Add the libraries that the chosen ones depend on
        $names = resolveLibraries($registry, jsLibraryProfiles(), $request);
        foreach ($names as $name) {
            foreach (jsLibraryDependencies()[$name] ?? [] as $needed) {
                $request[] = $needed;
            }
        }
        $names = resolveLibraries($registry, jsLibraryProfiles(), $request);

        $html = '';
        foreach ($names as $name) {
            $files = [$registry[$name]];

            // Select2 also needs its language file, loaded right after it
            if ($name === 'select2') {
                $files[] = select2LanguageFile();
            }

            foreach ($files as $file) {
                if (!is_file(PUBLIC_PATH . '/' . $file)) {
                    throw new RuntimeException("JS library file is missing: public/{$file}");
                }
                $html .= '<script src="' . e(asset($file, true)) . '"></script>' . PHP_EOL;
            }
        }

        // After Select2 is loaded, tell it which language to use
        if (in_array('select2', $names, true)) {
            $lang  = currentLocale() === 'MY' ? 'ms' : 'en';
            $html .= "<script>if (window.jQuery && jQuery.fn.select2) { jQuery.fn.select2.defaults.set('language', '{$lang}'); }</script>" . PHP_EOL;
        }
        return $html;
    }
}
