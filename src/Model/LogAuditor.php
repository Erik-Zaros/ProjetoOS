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

}
