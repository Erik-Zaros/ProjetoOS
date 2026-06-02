<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Db;

class TipoAnexoRepository
{
    public static function listarPorContexto(int $contextoAnexo): array
    {
        $con = Db::getConnection();
        $contexto = (int) $contextoAnexo;

        $sql = "SELECT tipo_anexo, descricao, ativo
                FROM tbl_tipo_anexo
                WHERE contexto_anexo = {$contexto}
                ORDER BY descricao";

        $res = pg_query($con, $sql);
        $rows = [];

        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }

        return $rows;
    }
}
