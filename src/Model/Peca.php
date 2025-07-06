<?php

namespace App\Model;

use App\Core\Db;

class Peca
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

        $codigo    = pg_escape_string($this->dados['codigo']);
        $descricao = pg_escape_string($this->dados['descricao']);
        $ativo     = ($this->dados['ativo'] === 't') ? 't' : 'f';
        $posto     = intval($this->posto);

        $sqlCheck = "SELECT 1 FROM tbl_peca WHERE codigo = '{$codigo}' AND descricao = '{$descricao}' AND posto = {$posto}";
        $check = pg_query($con, $sqlCheck);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Peça já cadastrado com esse código e descrição!'];
        }

        $sqlInsert = "INSERT INTO tbl_peca (codigo, descricao, ativo, posto)
                      VALUES ('{$codigo}', '{$descricao}', '{$ativo}', {$posto})";
        $insert = pg_query($con, $sqlInsert);

        return $insert
            ? ['status' => 'success', 'message' => 'Peça cadastrado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao cadastrar peça.'];
    }

    public static function buscarPorCodigo($codigo, $posto)
    {
        $con = Db::getConnection();
        $codigo = pg_escape_string($codigo);
        $posto = intval($posto);

        $sql = "SELECT peca, codigo, descricao, ativo FROM tbl_peca WHERE codigo = '{$codigo}' AND posto = {$posto}";
        $res = pg_query($con, $sql);

        return pg_num_rows($res) > 0
            ? pg_fetch_assoc($res)
            : ['success' => false, 'error' => 'Peça não encontrado.'];
    }

    public function atualizar()
    {
        $con = Db::getConnection();

        $codigo    = pg_escape_string($this->dados['codigo']);
        $descricao = pg_escape_string($this->dados['descricao']);
        $ativo     = ($this->dados['ativo'] === 't') ? 't' : 'f';
        $peca   = intval($this->dados['peca']);
        $posto     = intval($this->posto);

        $sqlCheck = "SELECT 1 FROM tbl_peca WHERE codigo = '{$codigo}' AND descricao = '{$descricao}' AND peca != {$peca} AND posto = {$posto}";
        $check = pg_query($con, $sqlCheck);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Já existe uma peça com esse código e descrição.'];
        }

        $sqlUpdate = "UPDATE tbl_peca SET codigo = '{$codigo}', descricao = '{$descricao}', ativo = '{$ativo}'
                      WHERE peca = {$peca} AND posto = {$posto}";
        $update = pg_query($con, $sqlUpdate);

        return $update
            ? ['status' => 'success', 'message' => 'Peça atualizado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao atualizar peça.'];
    }

    public static function listarTodos($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT peca, codigo, descricao, ativo FROM tbl_peca WHERE posto = {$posto} ORDER BY codigo ASC";
        $res = pg_query($con, $sql);

        $lista = [];
        while ($row = pg_fetch_assoc($res)) {
            $lista[] = $row;
        }

        return $lista;
    }

    public static function excluir($peca, $posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "DELETE FROM tbl_peca WHERE peca = $peca AND posto = $posto";
        $res = pg_query($con, $sql);

        return $res
            ? ['status' => 'success', 'message' => 'Peça excluido com sucesso']
            : ['status' => 'error', 'message' => 'Erro ao exlcuir peça.'];
    }
}
