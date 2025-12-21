<?php

namespace App\Model;

use App\Core\Db;

class LogAuditor
{
    public static function registrar(string $tabela, string $idRegistro, string $acao, array $antes = null, array $depois = null, int $usuarioId, int $postoId)
    {
        $con = Db::getConnection();

        $antesJson  = $antes  ? "'" . pg_escape_string(json_encode($antes, JSON_UNESCAPED_UNICODE)) . "'" : "NULL";
        $depoisJson = $depois ? "'" . pg_escape_string(json_encode($depois, JSON_UNESCAPED_UNICODE)) . "'" : "NULL";

        $sql = "
            INSERT INTO tbl_log_auditor (tabela, id_registro, acao, antes, depois, usuario, posto)
            VALUES (
                '{$tabela}',
                '{$idRegistro}',
                '{$acao}',
                {$antesJson},
                {$depoisJson},
                {$usuarioId},
                {$postoId}
            );
        ";

        pg_query($con, $sql);
    }

    public static function buscarPorRegistro(string $tabela, string $idRegistro, int $postoId): array
    {
        $con = Db::getConnection();

        $sql = "
            SELECT
                tbl_log_auditor.acao,
                tbl_log_auditor.antes,
                tbl_log_auditor.depois,
                to_char(tbl_log_auditor.data_log, 'DD/MM/YYYY HH24:MI') AS data_log,
                tbl_usuario.nome AS usuario_nome
            FROM tbl_log_auditor
            LEFT JOIN tbl_usuario ON tbl_usuario.usuario = tbl_log_auditor.usuario
            WHERE tbl_log_auditor.tabela = '$tabela'
            AND tbl_log_auditor.id_registro = '$idRegistro'
            AND tbl_log_auditor.posto = $postoId
            ORDER BY tbl_log_auditor.data_log DESC
        ";
        $res = pg_query($con, $sql);
        return pg_fetch_all($res) ?: [];
    }
}
