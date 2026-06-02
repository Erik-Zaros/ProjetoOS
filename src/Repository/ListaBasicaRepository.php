<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Db;

class ListaBasicaRepository
{
    public static function buscarPecasPorProduto($produtoId): array
    {
        $con = Db::getConnection();
        $produtoId = (int) $produtoId;

        $sql = "
            SELECT tbl_lista_basica.lista_basica, tbl_peca.peca, tbl_peca.codigo, tbl_peca.descricao, tbl_peca.ativo
            FROM tbl_lista_basica
            INNER JOIN tbl_peca ON tbl_lista_basica.peca = tbl_peca.peca
            WHERE tbl_lista_basica.produto = {$produtoId}
            ORDER BY tbl_peca.descricao ASC
        ";
        $res = pg_query($con, $sql);
        $pecas = [];

        while ($row = pg_fetch_assoc($res)) {
            $pecas[] = $row;
        }

        return $pecas;
    }

    public static function buscarProdutos($termo): array
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
        while ($row = pg_fetch_assoc($res)) {
            $produtos[] = $row;
        }

        return $produtos;
    }

    public static function buscarPecas($termo): array
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
        while ($row = pg_fetch_assoc($res)) {
            $pecas[] = $row;
        }

        return $pecas;
    }
}
