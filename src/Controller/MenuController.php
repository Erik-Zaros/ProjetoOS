<?php

namespace App\Controller;

use App\Core\Db;

class MenuController
{
    public static function estatisticasPorPosto($posto)
    {
        $con = Db::getConnection();

        $res = [];

        $sqlClientes = "SELECT COUNT(*) AS total FROM tbl_cliente WHERE posto = $1";
        $res['clientes'] = self::conta($con, $sqlClientes, [$posto]);

        $sqlProdutos = "SELECT COUNT(*) AS total FROM tbl_produto WHERE posto = $1";
        $res['produtos'] = self::conta($con, $sqlProdutos, [$posto]);

        $sqlPecas = "SELECT COUNT(*) AS total FROM tbl_peca WHERE posto = $1";
        $res['pecas'] = self::conta($con, $sqlPecas, [$posto]);

        $sqlOS = "SELECT COUNT(*) AS total FROM tbl_os WHERE posto = $1";
        $res['ordens_servico'] = self::conta($con, $sqlOS, [$posto]);

        $sqlUsuarios = "SELECT COUNT(*) AS total FROM tbl_usuario WHERE posto = $1";
        $res['usuarios'] = self::conta($con, $sqlUsuarios, [$posto]);

        $sqlProdutoAtivo = "SELECT COUNT(*) AS total FROM tbl_produto WHERE ativo = true AND posto = $1";
        $res['produto_ativo'] = self::conta($con, $sqlProdutoAtivo, [$posto]);

        $sqlProdutoInativo = "SELECT COUNT(*) AS total FROM tbl_produto WHERE ativo = false AND posto = $1";
        $res['produto_inativo'] = self::conta($con, $sqlProdutoInativo, [$posto]);

        $sqlPecaAtiva = "SELECT COUNT(*) AS total FROM tbl_peca WHERE ativo = true AND posto = $1";
        $res['peca_ativa'] = self::conta($con, $sqlPecaAtiva, [$posto]);

        $sqlPecaInativa = "SELECT COUNT(*) AS total FROM tbl_peca WHERE ativo = false AND posto = $1";
        $res['peca_inativa'] = self::conta($con, $sqlPecaInativa, [$posto]);

        $sqlStatusOS = "
            SELECT
                COUNT(*) FILTER (WHERE finalizada IS TRUE) AS finalizadas,
                COUNT(*) FILTER (WHERE (finalizada IS FALSE OR finalizada IS NULL) AND (cancelada IS FALSE OR cancelada IS NULL)) AS abertas,
                COUNT(*) FILTER (WHERE cancelada IS TRUE AND (finalizada IS FALSE OR finalizada IS NULL)) AS canceladas
            FROM tbl_os
            WHERE posto = $1
        ";

        $resStatusOs = pg_query_params($con, $sqlStatusOS, [$posto]);
        if ($resStatusOs && $row = pg_fetch_assoc($resStatusOs)) {
            $res['os_finalizadas'] = (int)$row['finalizadas'];
            $res['os_abertas'] = (int)$row['abertas'];
            $res['os_canceladas'] = (int)($row['canceladas']);
        } else {
            $res['os_finalizadas'] = 0;
            $res['os_abertas'] = 0;
            $res['os_canceladas'] = 0;
        }

        return $res;
    }

    private static function conta($con, $sql, $params)
    {
        $query = pg_query_params($con, $sql, $params);
        $row = pg_fetch_row($query);
        return (int) $row[0];
    }
}
