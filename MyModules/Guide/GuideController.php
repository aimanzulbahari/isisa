<?php
declare(strict_types=1);

/**
 * GuideController: the logic of the Guide page (Guide.php).
 *
 * Reads a README.md file and turns the Markdown into safe HTML with Parsedown.
 * Files are looked for in this order (the first one that exists is used):
 *   1. README.md (project root)
 *   2. MyModules/Guide/README.md
 *
 * The file path is fixed here, never taken from the URL, so a visitor cannot read other files.
 * Safe mode is ON: any raw HTML or script written inside the Markdown is escaped, not run.
 */

if (!function_exists('handleGuide')) {
    /**
     * @return array{html:string,source:?string,updated:?int}
     *         html    = the README as HTML ('' when there is no readable content)
     *         source  = which file was used (relative path), or null if none was found
     *         updated = last modified time of that file, or null
     */
    function handleGuide(): array
    {
        $candidates = [
            'README.md'                 => ROOT_PATH . '/README.md',
            'MyModules/Guide/README.md' => __DIR__ . '/README.md',
        ];

        foreach ($candidates as $label => $file) {
            if (!is_file($file) || !is_readable($file)) {
                continue;
            }

            $markdown = (string) file_get_contents($file);
            if (trim($markdown) === '') {
                // A file exists but is empty: remember it, keep looking for a better one
                $emptyFile ??= [$label, $file];
                continue;
            }

            $parser = new Parsedown();
            $parser->setSafeMode(true);

            return [
                'html'    => $parser->text($markdown),
                'source'  => $label,
                'updated' => filemtime($file) ?: null,
            ];
        }

        return [
            'html'    => '',
            'source'  => isset($emptyFile) ? $emptyFile[0] : null,
            'updated' => isset($emptyFile) ? (filemtime($emptyFile[1]) ?: null) : null,
        ];
    }
}
