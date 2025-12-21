<?php

namespace App\Model;

use App\Core\Db;
use App\Auth\Autenticador;
use App\Model\LogAuditor;

class ServicoRealizado
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

        $descricao    = pg_escape_string($this->dados['descricao']);
        $ativo     = ($this->dados['ativo'] === 't') ? 't' : 'f';
        $usa_estoque = ($this->dados['usa_estoque']  === 't') ? 't' : 'f';
        $posto     = intval($this->posto);

        $sqlCheck = "SELECT * FROM tbl_servico_realizado WHERE descricao = '{$descricao}' AND posto = {$posto}";
        $check = pg_query($con, $sqlCheck);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Serviço Realizado já cadastrado com essa descrição!'];
        }

        $sqlInsert = "INSERT INTO tbl_servico_realizado (descricao, ativo, usa_estoque, posto)
                      VALUES ('{$descricao}', '{$ativo}', '{$usa_estoque}', {$posto}) RETURNING servico_realizado";
        $insert = pg_query($con, $sqlInsert);

        if (pg_num_rows($insert) > 0) {
            $servico_realizado = pg_fetch_result($insert, 0, 'servico_realizado');
            $depois = [
                'descricao'    => $descricao,
                'ativo' => $ativo,
                'usa_estoque'     => $usa_estoque
            ];

            $antes = null;

            LogAuditor::registrar(
                'tbl_servico_realizado',
                $servico_realizado,
                'insert',
                $antes,
                $depois,
                $usuario,
                $posto
            );

            return ['status' => 'success', 'message' => 'Serviço Realizado cadastrado com sucesso!'];
        }
        return ['status' => 'error', 'message' => 'Erro ao cadastrar Serviço Realizado!'];
    }

    public static function buscarPorDescricao($descricao, $posto)
    {
        $con = Db::getConnection();
        $descricao = pg_escape_string($descricao);
        $posto = intval($posto);

        $sql = "SELECT servico_realizado, descricao, ativo, usa_estoque FROM tbl_servico_realizado WHERE descricao = '{$descricao}' AND posto = {$posto}";
        $res = pg_query($con, $sql);

        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : ['success' => false, 'error' => 'Serviço Realizado não encontrado.'];
    }

    public function atualizar()
    {
        $con = Db::getConnection();
        $usuario = Autenticador::getUsuario();

        $descricao = pg_escape_string($this->dados['descricao']);
        $ativo     = ($this->dados['ativo'] === 't') ? 't' : 'f';
        $usa_estoque     = ($this->dados['usa_estoque'] === 't') ? 't' : 'f';
        $servico_realizado   = intval($this->dados['servico_realizado']);
        $posto     = intval($this->posto);

        $sqlCheck = "SELECT 1 FROM tbl_servico_realizado WHERE descricao = '{$descricao}' AND servico_realizado != {$servico_realizado} AND posto = {$posto}";
        $check = pg_query($con, $sqlCheck);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Já existe um serviço realizado com essa descrição.'];
        }

        $sqlAntes = "SELECT descricao, ativo, usa_estoque FROM tbl_servico_realizado WHERE servico_realizado = $servico_realizado AND posto = $posto";
        $resAntes = pg_query($con, $sqlAntes);

        if (pg_num_rows($resAntes) > 0) {
            $descicaoAntes = pg_fetch_result($resAntes, 0, 'descricao');
            $ativoAntes = pg_fetch_result($resAntes, 0, 'ativo');
            $usaEstoqueAntes = pg_fetch_result($resAntes, 0, 'usa_estoque');

            $antes = [
                'descricao'    => $descicaoAntes,
                'ativo' => $ativoAntes,
                'usa_estoque' => $usaEstoqueAntes
            ];
        }

        $sqlUpdate = "UPDATE tbl_servico_realizado SET descricao = '{$descricao}', ativo = '{$ativo}', usa_estoque = '{$usa_estoque}'
                      WHERE servico_realizado = {$servico_realizado} AND posto = {$posto}";
        $update = pg_query($con, $sqlUpdate);

        if ($update) {
            $depois = [
                'descricao'    => $descricao,
                'ativo' => $ativo,
                'usa_estoque' => $usa_estoque
            ];

            LogAuditor::registrar(
                'tbl_servico_realizado',
                $servico_realizado,
                'update',
                $antes,
                $depois,
                $usuario,
                $posto
            );

        return ['status' => 'success', 'message' => 'Serviço Realizado atualizado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao atualizar Serviço Realizado.'];
    }

    public static function listarTodos($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT servico_realizado, descricao, ativo, usa_estoque FROM tbl_servico_realizado WHERE posto = {$posto} ORDER BY descricao ASC";
        $res = pg_query($con, $sql);

        $lista = [];
        while ($row = pg_fetch_assoc($res)) {
            $lista[] = $row;
        }

        return $lista;
    }

    public static function excluir($servico_realizado, $posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $valida_servico_realizado = self::validaServicoRealizado($servico_realizado, $posto);

        if ($valida_servico_realizado == false) {
            $sql = "DELETE FROM tbl_servico_realizado WHERE servico_realizado = $servico_realizado AND posto = $posto";
            $res = pg_query($con, $sql);

        return $res ? ['status' => 'success', 'message' => 'Serviço Realizado excluído com sucesso.'] : ['status' => 'error', 'message' => 'Erro ao excluir serviço Realizado.'];
        } else {
            return ['status' => 'error', 'message' => 'Não é possível excluir. O Serviço Realizado já tem vinculo com ordens de serviço.'];
        }
    }

    private static function validaServicoRealizado($servico_realizado, $posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT os
                FROM tbl_os_item
                WHERE servico_realizado = $servico_realizado
                AND posto = $posto
            ";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res) > 0) {
            return true;
        }

        return false;
    }
}
