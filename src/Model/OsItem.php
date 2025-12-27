<?php

namespace App\Model;

use App\Core\Db;

class OsItem
{
    public static function listarPorOs($os)
    {
        $con = Db::getConnection();
        $os = intval($os);

        $sql = "
            SELECT i.os_item, i.os, i.peca, i.quantidade, i.servico_realizado,
                   p.codigo, p.descricao, s.descricao as descricao_servico_realizado
            FROM tbl_os_item i
            INNER JOIN tbl_peca p ON i.peca = p.peca
            LEFT JOIN tbl_servico_realizado s ON s.servico_realizado = i.servico_realizado
            WHERE i.os = {$os}
            ORDER BY p.descricao ASC
        ";
        $res = pg_query($con, $sql);
        $itens = [];

        if ($res && pg_num_rows($res) > 0) {
            while ($row = pg_fetch_assoc($res)) {
                $itens[] = $row;
            }
        }

        return $itens;
    }

    public static function listarListaBasica($produto)
    {
        $con = Db::getConnection();
        $produto = intval($produto);

        $sql = "
            SELECT lb.peca, p.codigo, p.descricao
            FROM tbl_lista_basica lb
            INNER JOIN tbl_peca p ON lb.peca = p.peca
            WHERE lb.produto = {$produto}
            ORDER BY p.descricao ASC
        ";

        $res = pg_query($con, $sql);
        $pecas = [];

        if ($res && pg_num_rows($res) > 0) {
            while ($row = pg_fetch_assoc($res)) {
                $pecas[] = $row;
            }
        }

        return $pecas;
    }

    public static function remover($os_item, $os, $posto)
    {
        $con = Db::getConnection();
        $os_item = intval($os_item);
        $posto = intval($posto);

        if ($os_item <= 0) {
            return ['status' => 'error', 'message' => 'ID de item inválido.'];
        }

        $sql = "DELETE FROM tbl_os_item WHERE os_item = {$os_item} AND os = {$os} AND posto = {$posto}";
        $res = pg_query($con, $sql);

        if ($res && pg_affected_rows($res) > 0) {
            return ['status' => 'success', 'message' => 'Item removido com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Item não encontrado ou sem permissão para remover.'];
    }

    public static function buscarPecas($termo, $produto)
    {
        $con = Db::getConnection();
        $termo = pg_escape_string($con, trim($termo));
        $produto = intval($produto);

        if ($produto <= 0) {
            return [];
        }

        if (strlen($termo) < 2) {
            return [];
        }

        $sql = "
            SELECT p.peca, p.codigo, p.descricao
            FROM tbl_lista_basica lb
            INNER JOIN tbl_peca p ON lb.peca = p.peca
            WHERE lb.produto = {$produto}
            AND (p.codigo ILIKE '%{$termo}%' OR p.descricao ILIKE '%{$termo}%')
            AND p.ativo IS TRUE
            ORDER BY p.descricao ASC
            LIMIT 20
        ";
        $res = pg_query($con, $sql);
        $pecas = [];

        if ($res && pg_num_rows($res) > 0) {
            while ($row = pg_fetch_assoc($res)) {
                $pecas[] = [
                    'id'    => $row['peca'],
                    'label' => "{$row['codigo']} - {$row['descricao']}",
                    'value' => "{$row['codigo']} - {$row['descricao']}"
                ];
            }
        }

        return $pecas;
    }
}
