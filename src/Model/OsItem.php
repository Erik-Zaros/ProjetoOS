<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Db;

class OsItem
{
    public static function remover($os_item, $os, $posto)
    {
        $con = Db::getConnection();
        $os_item = (int) $os_item;
        $os = (int) $os;
        $posto = (int) $posto;

        if ($os_item <= 0) {
            return ['status' => 'error', 'message' => 'ID de item inválido.'];
        }

        $sql = "DELETE FROM tbl_os_item WHERE os_item = {$os_item} AND os = {$os} AND posto = {$posto}";
        $res = pg_query($con, $sql);

        if ($res && pg_affected_rows($res) > 0) {
            return ['status' => 'success', 'message' => 'Item removido com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Item não encontrado ou sem permissão para remover.'];
    }
}
