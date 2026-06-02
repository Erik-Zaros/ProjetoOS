<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Db;

class ServicoRealizadoRepository
{
    public static function buscarPorId($servicoRealizado, $posto): ?array
    {
        $con = Db::getConnection();
        $servicoRealizado = (int) trim((string) $servicoRealizado);
        $posto = (int) $posto;

        $sql = "SELECT servico_realizado, descricao, ativo, usa_estoque
                FROM tbl_servico_realizado
                WHERE servico_realizado = {$servicoRealizado} AND posto = {$posto}";
        $res = pg_query($con, $sql);

        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : null;
    }

    public static function listarTodos($posto): array
    {
        $con = Db::getConnection();
        $posto = (int) $posto;

        $sql = "SELECT servico_realizado, descricao, ativo, usa_estoque
                FROM tbl_servico_realizado WHERE posto = {$posto} ORDER BY descricao ASC LIMIT 500";
        $res = pg_query($con, $sql);

        $lista = [];
        while ($row = pg_fetch_assoc($res)) {
            $lista[] = $row;
        }

        return $lista;
    }

    public static function listarAtivos($posto): array
    {
        $con = Db::getConnection();
        $posto = (int) $posto;

        $sql = "SELECT servico_realizado, descricao, usa_estoque
                FROM tbl_servico_realizado
                WHERE posto = {$posto} 
                AND ativo IS TRUE
                ORDER BY descricao ASC";
        $res = pg_query($con, $sql);

        $lista = [];
        while ($row = pg_fetch_assoc($res)) {
            $lista[] = $row;
        }

        return $lista;
    }

    public static function temVinculoComOs($servicoRealizado, $posto): bool
    {
        $con = Db::getConnection();
        $servicoRealizado = (int) $servicoRealizado;
        $posto = (int) $posto;

        $sql = "SELECT 1 FROM tbl_os_item oi
                INNER JOIN tbl_os o ON o.os = oi.os
                WHERE oi.servico_realizado = {$servicoRealizado} AND o.posto = {$posto}
                LIMIT 1";
        $res = pg_query($con, $sql);

        return pg_num_rows($res) > 0;
    }
}
