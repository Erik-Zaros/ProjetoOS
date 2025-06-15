<?php

namespace App\Controller;

use App\Core\Db;

class MenuController
{
    public static function estatisticasPorPosto($posto)
    {
        $con = Db::getConnection();

        $res = [];

        $res['clientes'] = self::conta($con, "SELECT COUNT(*) FROM tbl_cliente WHERE posto = $1", [$posto]);
        $res['produtos'] = self::conta($con, "SELECT COUNT(*) FROM tbl_produto WHERE posto = $1", [$posto]);
        $res['ordens_servico'] = self::conta($con, "SELECT COUNT(*) FROM tbl_os WHERE posto = $1", [$posto]);
        $res['usuarios'] = self::conta($con, "SELECT COUNT(*) FROM tbl_usuario WHERE posto = $1", [$posto]);

        $res['produto_ativo'] = self::conta($con, "SELECT COUNT(*) FROM tbl_produto WHERE ativo = true AND posto = $1", [$posto]);
        $res['produto_inativo'] = self::conta($con, "SELECT COUNT(*) FROM tbl_produto WHERE ativo = false AND posto = $1", [$posto]);

        return $res;
    }

    private static function conta($con, $sql, $params)
    {
        $query = pg_query_params($con, $sql, $params);
        $row = pg_fetch_row($query);
        return (int) $row[0];
    }
}
