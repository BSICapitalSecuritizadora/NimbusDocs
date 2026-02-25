<?php

declare(strict_types=1);

namespace App\Application\Service;

class ExportService
{
    /**
     * Stream data as CSV to the output buffer.
     * Efficiently handles large datasets by iterating and writing line by line.
     *
     * @param iterable $dataGenerator Payload producing arrays
     * @param array $headers CSV Headers
     * @param string $filename Download filename
     */
    public function streamCsv(iterable $dataGenerator, array $headers, string $filename): void
    {
        // Headers for download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // BOM for Excel compatibility
        fputs($out, "\xEF\xBB\xBF");

        // Write Headers
        fputcsv($out, $headers, ';');

        // Write Data
        foreach ($dataGenerator as $row) {
            fputcsv($out, $row, ';');
        }

        fclose($out);
        exit;
    }

    /**
     * Stream data as an HTML table (Pseudo-Excel).
     *
     * @param iterable $dataGenerator
     * @param array $columns Field mapping (key => label/config)
     * @param string $title Report Title
     * @param string $filename
     */
    public function streamHtmlExcel(iterable $dataGenerator, array $columns, string $title, string $filename): void
    {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Start HTML Stream
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        // CSS for Excel
        echo '<style>
                .header-brand { background-color: #0f172a; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; height: 40px; vertical-align: middle; }
                .th-style { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-weight: bold; height: 30px; vertical-align: middle; text-align: center; }
                .td-style { border: 1px solid #cbd5e1; vertical-align: middle; padding: 5px; color: #334155; }
                .text-center { text-align: center; }
              </style>';
        echo '</head><body>';
        echo '<table border="1">';

        // Title Row
        $colCount = count($columns);
        echo "<tr><td colspan=\"{$colCount}\" class=\"header-brand\">" . htmlspecialchars($title) . '</td></tr>';

        // Header Row
        echo '<tr>';
        foreach ($columns as $colConfig) {
            $label = is_array($colConfig) ? $colConfig['label'] : $colConfig;
            echo '<th class="th-style">' . htmlspecialchars($label) . '</th>';
        }
        echo '</tr>';

        // Data Rows
        foreach ($dataGenerator as $row) {
            echo '<tr>';
            foreach ($columns as $key => $colConfig) {
                $value = $row[$key] ?? '';
                $style = is_array($colConfig) && isset($colConfig['style']) ? ' style="' . $colConfig['style'] . '"' : '';

                // Format if needed
                if (is_array($colConfig) && isset($colConfig['formatter']) && is_callable($colConfig['formatter'])) {
                    $value = $colConfig['formatter']($value, $row);
                }

                echo "<td class=\"td-style\"{$style}>" . htmlspecialchars((string) $value) . '</td>';
            }
            echo '</tr>';

            // Flush periodically to keep memory low
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }

        echo '</table></body></html>';
        exit;
    }
}
