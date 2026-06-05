<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Db;

class EstoqueRepository
{
    public static function saldo($posto, $produto = null, $peca = null)
    {
        $con = Db::getConnection();
        $posto = (int) $posto;

        if ($produto !== null) {
            $sql = "SELECT COALESCE(qtde,0) AS qt FROM tbl_estoque WHERE posto = {$posto} AND produto = " . (int) $produto;
        } elseif ($peca !== null) {
            $sql = "SELECT COALESCE(qtde,0) AS qt FROM tbl_estoque WHERE posto = {$posto} AND peca = " . (int) $peca;
        } else {
            return 0;
        }

        $res = pg_query($con, $sql);
        if (pg_num_rows($res) > 0) {
            return pg_fetch_result($res, 0, 'qt');
        }

        return 0;
    }

    public static function listarMovimentos($posto, $produto = null, $peca = null, $limit = 20): array
    {
        $con = Db::getConnection();
        $posto = (int) $posto;
        $limit = max(1, (int) $limit);

        if ($produto !== null) {
            $sql = "SELECT tbl_estoque_movimento.estoque_movimento,
                           to_char(tbl_estoque_movimento.data_input,'DD/MM/YYYY HH24:MI') AS data,
                           CASE WHEN tbl_estoque_movimento.tipo = 'E' THEN 'E' ELSE 'S' END AS tipo,
                           tbl_estoque_movimento.qtde,
                           tbl_estoque_movimento.os,
                           tbl_estoque_movimento.motivo,
                           tbl_produto.descricao AS item
                    FROM tbl_estoque_movimento
                    INNER JOIN tbl_produto ON tbl_produto.produto = tbl_estoque_movimento.produto AND tbl_produto.posto = tbl_estoque_movimento.posto
                    WHERE tbl_estoque_movimento.posto = {$posto}
                    AND tbl_estoque_movimento.produto = " . (int) $produto . "
                    ORDER BY tbl_estoque_movimento.estoque_movimento DESC
                    LIMIT {$limit}";
        } elseif ($peca !== null) {
            $sql = "SELECT tbl_estoque_movimento.estoque_movimento,
                           to_char(tbl_estoque_movimento.data_input,'DD/MM/YYYY HH24:MI') AS data,
                           CASE WHEN tbl_estoque_movimento.tipo = 'E' THEN 'E' ELSE 'S' END AS tipo,
                           tbl_estoque_movimento.qtde,
                           tbl_estoque_movimento.os,
                           tbl_estoque_movimento.motivo,
                           tbl_peca.descricao AS item
                    FROM tbl_estoque_movimento
                    INNER JOIN tbl_peca ON tbl_peca.peca = tbl_estoque_movimento.peca AND tbl_peca.posto = tbl_estoque_movimento.posto
                    WHERE tbl_estoque_movimento.posto = {$posto}
                    AND tbl_estoque_movimento.peca = " . (int) $peca . "
                    ORDER BY tbl_estoque_movimento.estoque_movimento DESC
                    LIMIT {$limit}";
        } else {
            return [];
        }

        $res = pg_query($con, $sql);
        $rows = [];
        if ($res) {
            while ($r = pg_fetch_assoc($res)) {
                $rows[] = $r;
            }
        }

        return $rows;
    }

    public static function consultarLista($posto, $tipo = 'ambos', $termo = '', $somenteSaldo = false): array
    {
        $con = Db::getConnection();
        $posto = (int) $posto;
        $whereSaldo = $somenteSaldo ? ' AND COALESCE(e.qtde,0) > 0 ' : '';
        $termo = trim($termo);
        $tEsc = $termo !== '' ? pg_escape_string($termo) : '';
        $fltProd = $termo !== '' ? " AND (p.codigo ILIKE '%{$tEsc}%' OR p.descricao ILIKE '%{$tEsc}%') " : '';
        $fltPeca = $termo !== '' ? " AND (pc.codigo ILIKE '%{$tEsc}%' OR pc.descricao ILIKE '%{$tEsc}%') " : '';
        $parts = [];

        if ($tipo === 'produto' || $tipo === 'ambos') {
            $parts[] = "
                SELECT 'PRODUTO' AS tipo_item, p.codigo, p.descricao, COALESCE(e.qtde,0) AS saldo,
                       (SELECT to_char(m.data_input,'DD/MM/YYYY HH24:MI') || ' ('|| m.qtde ||')'
                          FROM tbl_estoque_movimento m
                         WHERE m.posto = p.posto AND m.produto = p.produto AND m.tipo = 'E'
                         ORDER BY m.estoque_movimento DESC LIMIT 1) AS ult_entrada,
                       (SELECT to_char(m.data_input,'DD/MM/YYYY HH24:MI') || ' ('|| m.qtde ||')'
                          FROM tbl_estoque_movimento m
                         WHERE m.posto = p.posto AND m.produto = p.produto AND m.tipo = 'S'
                         ORDER BY m.estoque_movimento DESC LIMIT 1) AS ult_saida,
                       p.produto AS id, 'produto' AS kind
                  FROM tbl_produto p
                  LEFT JOIN tbl_estoque e ON e.posto = p.posto AND e.produto = p.produto
                 WHERE p.posto = {$posto} {$fltProd} {$whereSaldo}";
        }

        if ($tipo === 'peca' || $tipo === 'ambos') {
            $parts[] = "
                SELECT 'PEÇA' AS tipo_item, pc.codigo, pc.descricao, COALESCE(e.qtde,0) AS saldo,
                       (SELECT to_char(m.data_input,'DD/MM/YYYY HH24:MI') || ' ('|| m.qtde ||')'
                          FROM tbl_estoque_movimento m
                         WHERE m.posto = pc.posto AND m.peca = pc.peca AND m.tipo = 'E'
                         ORDER BY m.estoque_movimento DESC LIMIT 1) AS ult_entrada,
                       (SELECT to_char(m.data_input,'DD/MM/YYYY HH24:MI') || ' ('|| m.qtde ||')'
                          FROM tbl_estoque_movimento m
                         WHERE m.posto = pc.posto AND m.peca = pc.peca AND m.tipo = 'S'
                         ORDER BY m.estoque_movimento DESC LIMIT 1) AS ult_saida,
                       pc.peca AS id, 'peca' AS kind
                  FROM tbl_peca pc
                  LEFT JOIN tbl_estoque e ON e.posto = pc.posto AND e.peca = pc.peca
                 WHERE pc.posto = {$posto} {$fltPeca} {$whereSaldo}";
        }

        if (empty($parts)) {
            return [];
        }

        $sql = 'SELECT * FROM (' . implode(' UNION ALL ', $parts) . ') x ORDER BY x.descricao ASC';
        $res = pg_query($con, $sql);
        $rows = [];
        if ($res) {
            while ($r = pg_fetch_assoc($res)) {
                $r['saldo'] = (int) $r['saldo'];
                $rows[] = $r;
            }
        }

        return $rows;
    }
}
