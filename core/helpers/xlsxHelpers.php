<?php
declare(strict_types=1);

/**
 * Excel (.xlsx) helpers (PhpSpreadsheet).
 *
 * Export example:
 *   exportXlsx(['Name', 'Email'], [['Ali', 'ali@x.com'], ['Siti', 'siti@x.com']], 'users.xlsx', 'Users');
 *
 * Import example:
 *   $rows = readXlsx($path);   // [['Name' => 'Ali', 'Email' => 'ali@x.com'], ...]
 */

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!function_exists('exportXlsx')) {
    /**
     * Build an Excel file and send it to the browser as a download.
     *
     * @param string[]  $headers Column titles.
     * @param array[]   $rows    Rows of values (same order as the headers).
     */
    function exportXlsx(array $headers, array $rows, string $filename = 'export.xlsx', string $sheetTitle = 'Sheet1'): never
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr(preg_replace('/[\\\\\/?*\[\]:]/', ' ', $sheetTitle) ?? 'Sheet1', 0, 31));

        // Header row: bold, coloured, frozen
        foreach (array_values($headers) as $col => $title) {
            $sheet->setCellValueExplicit([$col + 1, 1], (string) $title, DataType::TYPE_STRING);
        }
        $lastCol = max(1, count($headers));
        $range   = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol) . '1';
        $sheet->getStyle($range)->getFont()->setBold(true);
        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E9ECEF');
        $sheet->freezePane('A2');

        // Data rows
        foreach (array_values($rows) as $r => $row) {
            foreach (array_values($row) as $col => $value) {
                $cell = [$col + 1, $r + 2];

                if (is_int($value) || is_float($value)) {
                    $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_NUMERIC);
                } else {
                    // Always write as TEXT. This stops "formula injection":
                    // a value like =HYPERLINK(...) will not run when opened in Excel.
                    $sheet->setCellValueExplicit($cell, (string) ($value ?? ''), DataType::TYPE_STRING);
                }
            }
        }

        for ($i = 1; $i <= $lastCol; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        // Clean the file name, remove earlier output, then send the file
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) ?: 'export.xlsx';
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        $spreadsheet->disconnectWorksheets();
        exit;
    }
}

if (!function_exists('readXlsx')) {
    /**
     * Read the first sheet of an Excel file.
     * The first row is used as the column names.
     *
     * @return array<int,array<string,mixed>> One array per data row. Empty rows are skipped.
     */
    function readXlsx(string $path): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true); // We only need values, not styles

        $spreadsheet = $reader->load($path);
        $data        = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        $spreadsheet->disconnectWorksheets();

        $headers = array_map(static fn($h) => trim((string) $h), array_shift($data) ?? []);
        $result  = [];

        foreach ($data as $row) {
            // Skip rows where every cell is empty
            if (count(array_filter($row, static fn($v) => !isBlank($v))) === 0) {
                continue;
            }
            $assoc = [];
            foreach ($headers as $i => $name) {
                if ($name !== '') {
                    $assoc[$name] = $row[$i] ?? null;
                }
            }
            $result[] = $assoc;
        }
        return $result;
    }
}
