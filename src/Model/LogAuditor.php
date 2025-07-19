<?php

namespace App\Model;

use App\Core\Db;

class LogAuditor
{
    public static function registrar(string $tabela, string $idRegistro, string $acao, array $antes = null, array $depois = null, int $usuarioId)
    {
        $con = Db::getConnection();

        $antesJson  = $antes  ? "'" . pg_escape_string(json_encode($antes, JSON_UNESCAPED_UNICODE)) . "'" : "NULL";
        $depoisJson = $depois ? "'" . pg_escape_string(json_encode($depois, JSON_UNESCAPED_UNICODE)) . "'" : "NULL";

        $sql = "
            INSERT INTO tbl_log_auditor (tabela, id_registro, acao, antes, depois, usuario)
            VALUES (
                '{$tabela}',
                '{$idRegistro}',
                '{$acao}',
                {$antesJson},
                {$depoisJson},
                {$usuarioId}
            );
        ";

        pg_query($con, $sql);
    }

    public static function buscarPorRegistro(string $tabela, string $idRegistro): array
    {
        $con = Db::getConnection();

        $sql = "
            SELECT
                l.acao,
                l.antes,
                l.depois,
                to_char(l.data_log, 'DD/MM/YYYY HH24:MI') AS data_log,
                u.nome AS usuario_nome
            FROM tbl_log_auditor l
            LEFT JOIN tbl_usuario u ON u.usuario = l.usuario
            WHERE l.tabela = '$tabela'
              AND l.id_registro = '$idRegistro'
            ORDER BY l.data_log DESC
        ";

        $res = pg_query($con, $sql);
        return pg_fetch_all($res) ?: [];
    }
}
