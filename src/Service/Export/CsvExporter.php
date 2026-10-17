<?php

namespace App\Service\Export;

class CsvExporter
{
    public function stream(string $filename, array $headers, iterable $linhas, string $delimiter = ';'): void
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, $headers, $delimiter, '"', '');

        foreach ($linhas as $linha) {
            fputcsv($out, array_map([$this, 'sanitizar'], $linha), $delimiter, '"', '');
        }

        fclose($out);
    }

    // CSV injection: células começando com = + - @ viram texto
    private function sanitizar($valor)
    {
        if (is_string($valor) && $valor !== '' && strpbrk($valor[0], "=+-@\t\r") !== false) {
            return "'" . $valor;
        }
        return $valor;
    }
}