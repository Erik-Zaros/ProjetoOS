<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Estoque;
use App\Repository\EstoqueRepository;

class EstoqueController
{
    public static function lancar(array $dados, $posto)
    {
        $m = new Estoque($dados, $posto);
        return $m->lancar();
    }

    public static function saldo($posto, $produto = null, $peca = null)
    {
        return ['saldo' => EstoqueRepository::saldo($posto, $produto, $peca)];
    }

    public static function listarMovimentos($posto, $produto = null, $peca = null, $limit = 20)
    {
        return EstoqueRepository::listarMovimentos($posto, $produto, $peca, $limit);
    }

    public static function consultar($posto, $tipo = 'ambos', $termo = '', $somenteSaldo = false)
    {
        $rows = EstoqueRepository::consultarLista($posto, $tipo, $termo, $somenteSaldo);

        $kpi = ['total' => 0, 'com_saldo' => 0, 'zerados' => 0, 'negativos' => 0];
        $kpi['total'] = count($rows);
        foreach ($rows as $r) {
            if ($r['saldo'] > 0) {
                $kpi['com_saldo']++;
            }
            if ($r['saldo'] == 0) {
                $kpi['zerados']++;
            }
            if ($r['saldo'] < 0) {
                $kpi['negativos']++;
            }
        }

        return ['status' => 'success', 'kpi' => $kpi, 'rows' => $rows];
    }
}
