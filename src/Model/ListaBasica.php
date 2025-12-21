<?php

namespace App\Model;

use App\Core\Db;

class ListaBasica
{
    public static function buscarPecasPorProduto($produtoId)
    {
        $con = Db::getConnection();
        $produtoId = intval($produtoId);

        $sql = "
            SELECT tbl_lista_basica.lista_basica,
                   tbl_peca.peca,
                   tbl_peca.codigo,
                   tbl_peca.descricao,
                   tbl_peca.ativo
            FROM tbl_lista_basica
            INNER JOIN tbl_peca ON tbl_lista_basica.peca = tbl_peca.peca
            WHERE tbl_lista_basica.produto = {$produtoId}
            ORDER BY tbl_peca.descricao ASC
        ";
        $res = pg_query($con, $sql);

        $pecas = [];

        if (pg_num_rows($res) > 0) {
            while ($row = pg_fetch_assoc($res)) {
                $pecas[] = $row;
            }
        }
        return $pecas;
    }

    public static function adicionarPeca($produtoId, $pecaId, $posto)
    {
        $con = Db::getConnection();
        $produtoId = intval($produtoId);
        $pecaId = intval($pecaId);
        $posto = intval($posto);

        $sql_valida = "SELECT peca FROM tbl_lista_basica WHERE posto = $posto AND produto = $produtoId AND peca = $pecaId";
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
        $listaBasicaId = intval($listaBasicaId);
        $sql = "DELETE FROM tbl_lista_basica WHERE lista_basica = {$listaBasicaId}";
        $res = pg_query($con, $sql);

        return $res ? ['success' => true] : ['success' => false];
    }

    public static function buscarProdutos($termo)
    {
        $con = Db::getConnection();
        $termo = pg_escape_string($con, $termo);

        $sql = "
            SELECT produto, codigo, descricao
            FROM tbl_produto
            WHERE codigo ILIKE '%{$termo}%' OR descricao ILIKE '%{$termo}%'
            ORDER BY descricao ASC
            LIMIT 10
        ";

        $res = pg_query($con, $sql);
        $produtos = [];
        if (pg_num_rows($res) > 0) {
            while ($row = pg_fetch_assoc($res)) {
                $produtos[] = $row;
            }
        }
        return $produtos;
    }

    public static function buscarPecas($termo)
    {
        $con = Db::getConnection();
        $termo = pg_escape_string($con, $termo);

        $sql = "
            SELECT peca, codigo, descricao
            FROM tbl_peca
            WHERE codigo ILIKE '%{$termo}%' OR descricao ILIKE '%{$termo}%'
            ORDER BY descricao ASC
            LIMIT 10
        ";

        $res = pg_query($con, $sql);
        $pecas = [];
        if (pg_num_rows($res) > 0) {
            while ($row = pg_fetch_assoc($res)) {
                $pecas[] = $row;
            }
        }
        return $pecas;
    }
}
