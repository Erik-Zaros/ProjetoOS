<?php

namespace App\Controller;

use App\Core\Db;

class MenuController
{
    public static function estatisticasPorPosto($posto)
    {
        $con = Db::getConnection();
        $res = [];

        $res['clientes']      = self::conta($con, "SELECT COUNT(*) FROM tbl_cliente WHERE posto = $1", [$posto]);
        $res['produtos']      = self::conta($con, "SELECT COUNT(*) FROM tbl_produto WHERE posto = $1", [$posto]);
        $res['pecas']         = self::conta($con, "SELECT COUNT(*) FROM tbl_peca WHERE posto = $1", [$posto]);
        $res['ordens_servico']= self::conta($con, "SELECT COUNT(*) FROM tbl_os WHERE posto = $1", [$posto]);
        $res['usuarios']      = self::conta($con, "SELECT COUNT(*) FROM tbl_usuario WHERE posto = $1", [$posto]);

        $sqlStatusOS = "
            SELECT
                COUNT(*) FILTER (WHERE finalizada IS TRUE) AS finalizadas,
                COUNT(*) FILTER (WHERE (finalizada IS FALSE OR finalizada IS NULL)
                             AND (cancelada IS FALSE OR cancelada IS NULL)) AS abertas,
                COUNT(*) FILTER (WHERE cancelada IS TRUE
                             AND (finalizada IS FALSE OR finalizada IS NULL)) AS canceladas
            FROM tbl_os
            WHERE posto = $1
        ";
        $rowOS = pg_fetch_assoc(pg_query_params($con, $sqlStatusOS, [$posto]));
        $res['os_finalizadas'] = (int)($rowOS['finalizadas'] ?? 0);
        $res['os_abertas']     = (int)($rowOS['abertas']     ?? 0);
        $res['os_canceladas']  = (int)($rowOS['canceladas']  ?? 0);

        $res['produto_ativo']   = self::conta($con, "SELECT COUNT(*) FROM tbl_produto WHERE ativo = true  AND posto = $1", [$posto]);
        $res['produto_inativo'] = self::conta($con, "SELECT COUNT(*) FROM tbl_produto WHERE ativo = false AND posto = $1", [$posto]);
        $res['peca_ativa']      = self::conta($con, "SELECT COUNT(*) FROM tbl_peca WHERE ativo = true  AND posto = $1", [$posto]);
        $res['peca_inativa']    = self::conta($con, "SELECT COUNT(*) FROM tbl_peca WHERE ativo = false AND posto = $1", [$posto]);

        $sqlTecnico = "
            SELECT u.nome, COUNT(o.os) AS total
            FROM tbl_os o
            JOIN tbl_usuario u ON u.usuario = o.tecnico
            WHERE o.posto = $1
            GROUP BY u.nome
            ORDER BY total DESC
            LIMIT 8
        ";
        $resTecnico = pg_query_params($con, $sqlTecnico, [$posto]);
        $res['os_por_tecnico'] = [];
        while ($row = pg_fetch_assoc($resTecnico)) {
            $res['os_por_tecnico'][] = [
                'nome'  => $row['nome'],
                'total' => (int)$row['total']
            ];
        }

        $sqlEstoque = "
            SELECT
                TO_CHAR(DATE_TRUNC('month', data_input), 'Mon/YY') AS mes,
                DATE_TRUNC('month', data_input) AS mes_ordem,
                SUM(CASE WHEN tipo = 'E' THEN qtde ELSE 0 END) AS entradas,
                SUM(CASE WHEN tipo = 'S' THEN qtde ELSE 0 END) AS saidas
            FROM tbl_estoque_movimento
            WHERE posto = $1
              AND data_input >= NOW() - INTERVAL '6 months'
            GROUP BY mes_ordem, mes
            ORDER BY mes_ordem ASC
        ";
        $resEstoque = pg_query_params($con, $sqlEstoque, [$posto]);
        $res['estoque_mensal'] = [];
        while ($row = pg_fetch_assoc($resEstoque)) {
            $res['estoque_mensal'][] = [
                'mes'      => $row['mes'],
                'entradas' => (int)$row['entradas'],
                'saidas'   => (int)$row['saidas']
            ];
        }

        $sqlPecas = "
            SELECT p.descricao, SUM(oi.quantidade) AS total
            FROM tbl_os_item oi
            JOIN tbl_peca p ON p.peca = oi.peca
            JOIN tbl_os o ON o.os = oi.os
            WHERE o.posto = $1
            GROUP BY p.descricao
            ORDER BY total DESC
            LIMIT 8
        ";
        $resPecas = pg_query_params($con, $sqlPecas, [$posto]);
        $res['pecas_mais_usadas'] = [];
        while ($row = pg_fetch_assoc($resPecas)) {
            $res['pecas_mais_usadas'][] = [
                'descricao' => $row['descricao'],
                'total'     => (int)$row['total']
            ];
        }

        $sqlTimeline = "
            SELECT
                TO_CHAR(data_abertura, 'DD/MM') AS dia,
                data_abertura,
                COUNT(*) AS total
            FROM tbl_os
            WHERE posto = $1
              AND data_abertura >= CURRENT_DATE - INTERVAL '29 days'
            GROUP BY data_abertura, dia
            ORDER BY data_abertura ASC
        ";
        $resTimeline = pg_query_params($con, $sqlTimeline, [$posto]);
        $res['os_timeline'] = [];
        while ($row = pg_fetch_assoc($resTimeline)) {
            $res['os_timeline'][] = [
                'dia'   => $row['dia'],
                'total' => (int)$row['total']
            ];
        }

        return $res;
    }

    private static function conta($con, $sql, $params)
    {
        $query = pg_query_params($con, $sql, $params);
        $row   = pg_fetch_row($query);
        return (int)$row[0];
    }
}
