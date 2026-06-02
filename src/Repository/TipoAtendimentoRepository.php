<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Db;

class TipoAtendimentoRepository
{
    public static function buscarPorId($tipoAtendimento, $posto): ?array
    {
        $con = Db::getConnection();
        $tipoAtendimento = (int) $tipoAtendimento;
        $posto = (int) $posto;

        $sql = "SELECT tipo_atendimento, codigo, descricao, ativo
                FROM tbl_tipo_atendimento
                WHERE tipo_atendimento = {$tipoAtendimento} AND posto = {$posto}";
        $res = pg_query($con, $sql);

        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : null;
    }

    public static function listarTodos($posto): array
    {
        $con = Db::getConnection();
        $posto = (int) $posto;

        $sql = "SELECT tipo_atendimento, codigo, descricao, ativo
                FROM tbl_tipo_atendimento WHERE posto = {$posto} ORDER BY descricao ASC";
        $res = pg_query($con, $sql);

        $lista = [];
        while ($row = pg_fetch_assoc($res)) {
            $lista[] = $row;
        }

        return $lista;
    }
}
