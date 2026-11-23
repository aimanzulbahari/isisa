<?php
declare(strict_types=1);

/**
 * PDF helpers (mPDF).
 *
 * Example:
 *   $html = renderTemplate('pdf/report.php', ['rows' => $rows]);
 *   generatePdf($html, 'report.pdf');            // download in the browser
 *   generatePdf($html, 'report.pdf', 'inline');  // open in the browser tab
 *   generatePdf($html, 'report.pdf', 'save');    // save in storage/temp, returns the path
 */

use Mpdf\Mpdf;
use Mpdf\Output\Destination;

if (!function_exists('createPdf')) {
    /** Build an mPDF object with our defaults (A4, 15mm margins). */
    function createPdf(array $options = []): Mpdf
    {
        $tmp = TEMP_PATH . '/mpdf';
        if (!is_dir($tmp)) {
            @mkdir($tmp, 0775, true);
        }

        return new Mpdf(array_merge([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'margin_left'   => 15,
            'margin_right'  => 15,
            'tempDir'       => $tmp,
        ], $options));
    }
}

if (!function_exists('generatePdf')) {
    /**
     * Turn HTML into a PDF.
     *
     * @param string $mode download | inline | save | string
     * @return string|null File path for "save", PDF content for "string", otherwise null.
     */
    function generatePdf(string $html, string $filename = 'document.pdf', string $mode = 'download', array $options = []): ?string
    {
        // Make the file name safe for headers and disk
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) ?: 'document.pdf';
        if (!str_ends_with(strtolower($filename), '.pdf')) {
            $filename .= '.pdf';
        }

        $pdf = createPdf($options);
        $pdf->SetTitle(pathinfo($filename, PATHINFO_FILENAME));
        $pdf->SetAuthor((string) config('app.name'));
        $pdf->WriteHTML($html);

        // Remove any earlier output, or the PDF file will be broken
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        switch ($mode) {
            case 'inline':
                $pdf->Output($filename, Destination::INLINE);
                return null;
            case 'save':
                $path = TEMP_PATH . '/' . $filename;
                $pdf->Output($path, Destination::FILE);
                return $path;
            case 'string':
                return $pdf->Output($filename, Destination::STRING_RETURN);
            default:
                $pdf->Output($filename, Destination::DOWNLOAD);
                return null;
        }
    }
}
