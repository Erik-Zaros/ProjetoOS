<?php

declare(strict_types=1);

namespace App\Controller\Relatorio;

use App\Controller\MenuController;

class RelatorioMenuController
{
    public static function gerarCSV($posto): void
    {
        $dados = MenuController::estatisticasPorPosto($posto);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="dashboard_resumo.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Indicador', 'Valor'], ';');
        fputcsv($out, ['Ordens de Serviço', $dados['ordens_servico'] ?? 0], ';');
        fputcsv($out, ['OS Abertas', $dados['os_abertas'] ?? 0], ';');
        fputcsv($out, ['OS Finalizadas', $dados['os_finalizadas'] ?? 0], ';');
        fputcsv($out, ['OS Canceladas', $dados['os_canceladas'] ?? 0], ';');
        fputcsv($out, ['Clientes', $dados['clientes'] ?? 0], ';');
        fputcsv($out, ['Produtos', $dados['produtos'] ?? 0], ';');
        fputcsv($out, ['Peças', $dados['pecas'] ?? 0], ';');
        fputcsv($out, ['Usuários', $dados['usuarios'] ?? 0], ';');
        fclose($out);
        exit;
    }
}
