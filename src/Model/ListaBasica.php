<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Db;

class ListaBasica
{
    public static function adicionarPeca($produtoId, $pecaId, $posto)
    {
        $con = Db::getConnection();
        $produtoId = (int) $produtoId;
        $pecaId = (int) $pecaId;
        $posto = (int) $posto;

        $sql_valida = "SELECT peca FROM tbl_lista_basica WHERE posto = {$posto} AND produto = {$produtoId} AND peca = {$pecaId}";
        $res_valida = pg_query($con, $sql_valida);

        if (pg_num_rows($res_valida) > 0) {
            return ['error' => true];
        }

        $sql = "
            INSERT INTO tbl_lista_basica (produto, peca, posto)
            VALUES ({$produtoId}, {$pecaId}, {$posto})
            ON CONFLICT (produto, peca) DO NOTHING
        ";

        $res = pg_query($con, $sql);
        return $res ? ['success' => true] : ['success' => false];
    }

    public static function removerPeca($listaBasicaId)
    {
        $con = Db::getConnection();
        $listaBasicaId = (int) $listaBasicaId;
        $sql = "DELETE FROM tbl_lista_basica WHERE lista_basica = {$listaBasicaId}";
        $res = pg_query($con, $sql);

        return $res ? ['success' => true] : ['success' => false];
    }
}
