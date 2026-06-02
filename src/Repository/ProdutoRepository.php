<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Db;

class ProdutoRepository
{
    public static function buscarPorId($produto, $posto): ?array
    {
        $con = Db::getConnection();
        $produto = (int) $produto;
        $posto = (int) $posto;

        $sql = "SELECT produto, codigo, descricao, ativo FROM tbl_produto WHERE produto = {$produto} AND posto = {$posto}";
        $res = pg_query($con, $sql);

        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : null;
    }

    public static function listarTodos($posto): array
    {
        $con = Db::getConnection();
        $posto = (int) $posto;

        $sql = "SELECT produto, codigo, descricao, ativo FROM tbl_produto WHERE posto = {$posto} ORDER BY codigo ASC LIMIT 500";
        $res = pg_query($con, $sql);

        $lista = [];
        while ($row = pg_fetch_assoc($res)) {
            $lista[] = $row;
        }

        return $lista;
    }

    public static function autocomplete($termo, $posto): array
    {
        $con = Db::getConnection();
        $termo = pg_escape_string($termo);
        $posto = (int) $posto;

        $sql = "SELECT produto, codigo, descricao
                FROM tbl_produto
                WHERE posto = {$posto}
                AND (descricao ILIKE '%{$termo}%' OR codigo ILIKE '%{$termo}%')
                ORDER BY descricao
                LIMIT 20";

        $res = pg_query($con, $sql);
        $sugestoes = [];

        while ($row = pg_fetch_assoc($res)) {
            $sugestoes[] = [
                'label'   => $row['descricao'] . ' (' . $row['codigo'] . ')',
                'value'   => $row['descricao'],
                'produto' => $row['produto'],
                'codigo'  => $row['codigo'],
            ];
        }

        return $sugestoes;
    }

    public static function produtoTemEstoque($produto, $posto): bool
    {
        $con = Db::getConnection();
        $produto = (int) $produto;
        $posto = (int) $posto;

        $sql = "SELECT qtde FROM tbl_estoque WHERE produto = {$produto} AND posto = {$posto}";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res) > 0) {
            $row = pg_fetch_assoc($res);
            return (float) $row['qtde'] > 0;
        }

        return false;
    }
}
