<?php

namespace App\Support\Exports;

class SimpleTableExporter
{
    public static function download(string $baseFilename, array $headings, iterable $rows, callable $mapRow, string $format = 'csv')
    {
        $format = strtolower($format);

        if (in_array($format, ['excel', 'xls'], true)) {
            return self::downloadExcel($baseFilename, $headings, $rows, $mapRow);
        }

        if ($format !== 'csv') {
            abort(404);
        }

        return self::downloadCsv($baseFilename, $headings, $rows, $mapRow);
    }

    private static function downloadCsv(string $baseFilename, array $headings, iterable $rows, callable $mapRow)
    {
        $filename = self::filename($baseFilename, 'csv');

        return response()->stream(function () use ($headings, $rows, $mapRow) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $headings);

            foreach ($rows as $row) {
                fputcsv($file, array_map([self::class, 'safeSpreadsheetValue'], $mapRow($row)));
            }

            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private static function downloadExcel(string $baseFilename, array $headings, iterable $rows, callable $mapRow)
    {
        $filename = self::filename($baseFilename, 'xls');

        return response()->stream(function () use ($headings, $rows, $mapRow) {
            echo '<!doctype html><html><head><meta charset="UTF-8"></head><body><table border="1">';
            echo '<thead><tr>';

            foreach ($headings as $heading) {
                echo '<th>' . e($heading) . '</th>';
            }

            echo '</tr></thead><tbody>';

            foreach ($rows as $row) {
                echo '<tr>';

                foreach ($mapRow($row) as $value) {
                    echo '<td>' . e(self::safeSpreadsheetValue($value)) . '</td>';
                }

                echo '</tr>';
            }

            echo '</tbody></table></body></html>';
        }, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private static function filename(string $baseFilename, string $extension): string
    {
        return $baseFilename . '_' . date('Ymd_His') . '.' . $extension;
    }

    private static function safeSpreadsheetValue($value): string
    {
        $value = (string) $value;

        if ($value !== '' && preg_match('/^[=\-+@]/', $value) === 1) {
            return "'" . $value;
        }

        return $value;
    }
}
