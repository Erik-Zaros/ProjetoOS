<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Db;

class LogAuditorRepository
{
    public static function buscarPorRegistro(string $tabela, string $idRegistro, int $postoId): array
    {
        $con = Db::getConnection();
        $tabela = pg_escape_string($tabela);
        $idRegistro = pg_escape_string($idRegistro);
        $postoId = (int) $postoId;

        $sql = "
            SELECT
                tbl_log_auditor.acao,
                tbl_log_auditor.antes,
                tbl_log_auditor.depois,
                to_char(tbl_log_auditor.data_log, 'DD/MM/YYYY HH24:MI') AS data_log,
                tbl_usuario.nome AS usuario_nome
            FROM tbl_log_auditor
            LEFT JOIN tbl_usuario ON tbl_usuario.usuario = tbl_log_auditor.usuario
            WHERE tbl_log_auditor.tabela = '{$tabela}'
            AND tbl_log_auditor.id_registro = '{$idRegistro}'
            AND tbl_log_auditor.posto = {$postoId}
            ORDER BY tbl_log_auditor.data_log DESC
        ";

        $res = pg_query($con, $sql);
        return pg_fetch_all($res) ?: [];
    }
}
