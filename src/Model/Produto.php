<?php

namespace App\Model;

use App\Core\Db;

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

        $check = pg_query_params($con,
            "SELECT 1 FROM tbl_produto WHERE codigo = $1 AND descricao = $2 AND posto = $3",
            [$this->dados['codigo'], $this->dados['descricao'], $this->posto]
        );

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Produto já cadastrado com esse código e descrição!'];
        }

        $insert = pg_query_params($con,
            "INSERT INTO tbl_produto (codigo, descricao, ativo, posto) VALUES ($1, $2, $3, $4)",
            [$this->dados['codigo'], $this->dados['descricao'], $this->dados['ativo'] === 't', $this->posto]
        );

        return $insert
            ? ['status' => 'success', 'message' => 'Produto cadastrado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao cadastrar produto.'];
    }

    public static function buscarPorCodigo($codigo, $posto)
    {
        $con = Db::getConnection();
        $res = pg_query_params($con,
            "SELECT produto, codigo, descricao, ativo FROM tbl_produto WHERE codigo = $1 AND posto = $2",
            [$codigo, $posto]
        );

        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : ['success' => false, 'error' => 'Produto não encontrado.'];
    }

    public function atualizar()
    {
        $con = Db::getConnection();

        $check = pg_query_params($con,
            "SELECT 1 FROM tbl_produto WHERE codigo = $1 AND descricao = $2 AND produto != $3 AND posto = $4",
            [$this->dados['codigo'], $this->dados['descricao'], $this->dados['produto'], $this->posto]
        );

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Já existe um produto com esse código e descrição.'];
        }

        $update = pg_query_params($con,
            "UPDATE tbl_produto SET codigo = $1, descricao = $2, ativo = $3 WHERE produto = $4 AND posto = $5",
            [$this->dados['codigo'], $this->dados['descricao'], $this->dados['ativo'] === 't', $this->dados['produto'], $this->posto]
        );

        return $update
            ? ['status' => 'success', 'message' => 'Produto atualizado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao atualizar produto.'];
    }

    public static function listarTodos($posto)
    {
        $con = Db::getConnection();
        $res = pg_query_params($con,
            "SELECT produto, codigo, descricao, ativo FROM tbl_produto WHERE posto = $1 ORDER BY codigo ASC",
            [$posto]
        );

        $lista = [];
        while ($row = pg_fetch_assoc($res)) {
            $lista[] = $row;
        }

        return $lista;
    }
}
