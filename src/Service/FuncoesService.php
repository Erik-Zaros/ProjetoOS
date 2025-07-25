<?php

namespace App\Service;

use App\Core\Db;

class FuncoesService
{
    public static function buscaNomePosto($postoId)
    {
        $con = Db::getConnection();
        $sql = "SELECT UPPER(nome) AS nome
                FROM tbl_posto
                WHERE posto = $postoId
            ";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res) > 0) {
            $nome_posto = pg_fetch_result($res, 0, 'nome');
            return $nome_posto;
        }
    }
}
