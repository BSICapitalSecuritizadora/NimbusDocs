<?php

declare(strict_types=1);

namespace App\Support;

final class CsvResponse
{
    /**
     * Compatível com chamada do controller: CsvResponse::output($fields, $data, $filename)
     */
    public static function output(array $fields, array $data, string $filename = 'export.csv'): void
    {
        // Remove extensão .csv se já estiver presente
        $filename = preg_replace('/\.csv$/i', '', $filename);
        self::send($filename, $fields, $data);
    }
    /**
     * @param string   $filename  Nome do arquivo (sem .csv)
     * @param string[] $headers   Cabeçalhos das colunas
     * @param iterable $rows      Linhas (cada linha: array na mesma ordem de $headers)
     */
    public static function send(string $filename, array $headers, iterable $rows): void
    {
        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename) . '_' . date('Ymd_His');
        $fullName = $safeName . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $fullName . '"');

        // BOM UTF-8 para Excel brasileiro entender acentuação
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');
        if ($out === false) {
            throw new \RuntimeException('Não foi possível abrir o output CSV.');
        }

        // Cabeçalho
        fputcsv($out, $headers, ';');

        foreach ($rows as $row) {
            fputcsv($out, $row, ';');
        }

        fclose($out);
        exit;
    }
}
