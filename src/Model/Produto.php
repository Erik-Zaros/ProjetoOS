<?php

namespace App\Model;

use App\Core\Db;
use App\Auth\Autenticador;
use App\Model\LogAuditor;

class Produto
{
    private $dados;
    private $posto;

    public function __construct(array $dados, $posto)
    {
        $this->dados = $dados;
        $this->posto = $posto;
    }

    public function salvar()
    {
        $con = Db::getConnection();
        $usuario = Autenticador::getUsuario();

        $codigo    = pg_escape_string($this->dados['codigo']);
        $descricao = pg_escape_string($this->dados['descricao']);
        $ativo     = ($this->dados['ativo'] === 't') ? 't' : 'f';
        $posto     = intval($this->posto);

        $sqlCheck = "SELECT 1 FROM tbl_produto WHERE codigo = '{$codigo}' AND descricao = '{$descricao}' AND posto = {$posto}";
        $check = pg_query($con, $sqlCheck);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Produto já cadastrado com esse código e descrição!'];
        }

        $sqlInsert = "INSERT INTO tbl_produto (codigo, descricao, ativo, posto)
                      VALUES ('{$codigo}', '{$descricao}', '{$ativo}', {$posto}) RETURNING produto";
        $insert = pg_query($con, $sqlInsert);

        if ($insert && pg_num_rows($insert) > 0) {
            $produto = pg_fetch_result($insert, 0, 'produto');
            $depois = [
                'codigo'    => $codigo,
                'descricao' => $descricao,
                'ativo'     => $ativo
            ];

            $antes = null;

            LogAuditor::registrar(
                'tbl_produto',
                $produto,
                'insert',
                $antes,
                $depois,
                $usuario,
                $posto
            );

            return ['status' => 'success', 'message' => 'Produto cadastrado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao cadastrar produto!'];
    }

    public static function buscarPorProduto($produto, $posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT produto, codigo, descricao, ativo FROM tbl_produto WHERE produto = {$produto} AND posto = {$posto}";
        $res = pg_query($con, $sql);

        return pg_num_rows($res) > 0
            ? pg_fetch_assoc($res)
            : ['success' => false, 'error' => 'Produto não encontrado.'];
    }

    public function atualizar()
    {
        $con = Db::getConnection();
        $usuario = Autenticador::getUsuario();

        $codigo    = pg_escape_string($this->dados['codigo']);
        $descricao = pg_escape_string($this->dados['descricao']);
        $ativo     = ($this->dados['ativo'] === 't') ? 't' : 'f';
        $produto   = intval($this->dados['produto']);
        $posto     = intval($this->posto);

        $sqlCheck = "SELECT 1 FROM tbl_produto WHERE codigo = '{$codigo}' AND descricao = '{$descricao}' AND produto != {$produto} AND posto = {$posto}";
        $check = pg_query($con, $sqlCheck);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Já existe um produto com esse código e descrição.'];
        }

        $sqlAntes = "SELECT codigo, descricao, ativo FROM tbl_produto WHERE produto = $produto AND posto = $posto";
        $resAntes = pg_query($con, $sqlAntes);

        if (pg_num_rows($resAntes) > 0) {
            $codigoAntes = pg_fetch_result($resAntes, 0, 'codigo');
            $codigoAntes = pg_fetch_result($resAntes, 0, 'descricao');
            $ativoAntes = pg_fetch_result($resAntes, 0, 'ativo');

            $antes = [
                'codigo'    => $codigoAntes,
                'descricao' => $codigoAntes,
                'ativo'     => $ativoAntes
            ];
        }

        $sqlUpdate = "UPDATE tbl_produto SET codigo = '{$codigo}', descricao = '{$descricao}', ativo = '{$ativo}'
                      WHERE produto = {$produto} AND posto = {$posto}";
        $update = pg_query($con, $sqlUpdate);

        if ($update) {
            $depois = [
                'codigo'    => $codigo,
                'descricao' => $descricao,
                'ativo'     => $ativo
            ];

            LogAuditor::registrar(
                'tbl_produto',
                $produto,
                'update',
                $antes,
                $depois,
                $usuario,
                $posto
            );

        return ['status' => 'success', 'message' => 'Produto atualizado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao atualizar produto.'];
    }

    public static function listarTodos($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT produto, codigo, descricao, ativo FROM tbl_produto WHERE posto = {$posto} ORDER BY codigo ASC";
        $res = pg_query($con, $sql);

        $lista = [];
        while ($row = pg_fetch_assoc($res)) {
            $lista[] = $row;
        }

        return $lista;
    }

    public static function excluir($produto, $posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $produto_tem_estoque = self::produtoTemEstoque($produto, $posto);

        if ($produto_tem_estoque == false) {
        $sql = "DELETE FROM tbl_produto WHERE produto = $produto AND posto = $posto";
        $res = pg_query($con, $sql);

        return $res ? ['status' => 'success', 'message' => 'Produto excluído com sucesso.'] : ['status' => 'error', 'message' => 'Erro ao excluir produto.'];
        } else {
            return ['status' => 'error', 'message' => 'Não é possível excluir. O produto ainda está vinculada ao estoque.'];
        }
    }

    public static function autocompleteProdutos($termo, $posto)
    {
        $con   = Db::getConnection();
        $termo = pg_escape_string($termo);
        $posto = intval($posto);

        $sql = "SELECT produto,
                       codigo,
                       descricao
                FROM tbl_produto
                WHERE posto = {$posto}
                AND (descricao ILIKE '%{$termo}%' OR codigo ILIKE '%{$termo}%')
                ORDER BY descricao
                LIMIT 20
            ";

        $res = pg_query($con, $sql);
        $sugestoes = [];

        while ($row = pg_fetch_assoc($res)) {
            $sugestoes[] = [
                'label'   => $row['descricao'] . " (" . $row['codigo'] . ")",
                'value'   => $row['descricao'],
                'produto' => $row['produto'],
                'codigo'  => $row['codigo']
            ];
        }
        return $sugestoes;
    }

    private static function produtoTemEstoque($produto, $posto)
    {
        $con = Db::getConnection();
        $produto = intval($produto);
        $posto = intval($posto);

        $sql = "SELECT qtde
                FROM tbl_estoque
                WHERE produto = $produto
                AND posto = $posto
            ";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res) > 0) {
            $row = pg_fetch_assoc($res);
            return $row['qtde'] > 0;
        }

        return false;
    }
}
