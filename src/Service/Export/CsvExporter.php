<?php

namespace App\Service\Export;

class CsvExporter
{
    public function stream(string $filename, array $headers, iterable $linhas, string $delimiter = ';'): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, $headers, $delimiter);

        foreach ($linhas as $linha) {
            fputcsv($output, $linha, $delimiter);
        }

        fclose($output);
        exit;
    }
}
