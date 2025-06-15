<?php

namespace App\Service;

use App\Core\Db;

class FuncoesService
{
    public static function buscaNomePosto($postoId)
    {
        $con = Db::getConnection();
        $sql = "SELECT nome FROM tbl_posto WHERE posto = $postoId";
        $res = pg_query($con, $sql);

        if ($res && pg_num_rows($res) > 0) {
            $row = pg_fetch_assoc($res);
            return $row['nome'];
        }

        return 'Posto não encontrado';
    }
}
