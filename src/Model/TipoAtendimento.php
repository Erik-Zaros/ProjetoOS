<?php

namespace App\Model;

use App\Core\Db;
use App\Auth\Autenticador;
use App\Model\LogAuditor;

class TipoAtendimento
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

        $codigo = pg_escape_string($this->dados['codigo']);
        $descricao = pg_escape_string($this->dados['descricao']);
        $ativo = ($this->dados['ativo'] === 't') ? 't' : 'f';
        $posto = intval($this->posto);

        $valida = $this->validaCamposTipoAtendimento($codigo, $descricao);
        if ($valida != null) {
            return ['status' => 'alert', 'message' => $valida];
        }

        $sqlCheck = "SELECT * FROM tbl_tipo_atendimento WHERE codigo = '{$codigo}' AND posto = {$posto}";
        $check = pg_query($con, $sqlCheck);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Tipo de Atendimento já cadastrado com esse código!'];
        }

        $sqlInsert = "INSERT INTO tbl_tipo_atendimento (codigo, descricao, ativo, posto)
                      VALUES ('{$codigo}', '{$descricao}', '{$ativo}', {$posto}) RETURNING tipo_atendimento";
        $insert = pg_query($con, $sqlInsert);

        if (pg_num_rows($insert) > 0) {
            $tipo_atendimento = pg_fetch_result($insert, 0, 'tipo_atendimento');
            $depois = [
                'codigo' => $codigo,
                'descricao' => $descricao,
                'ativo' => $ativo
            ];

            $antes = null;

            LogAuditor::registrar(
                'tbl_tipo_atendimento',
                $tipo_atendimento,
                'insert',
                $antes,
                $depois,
                $usuario,
                $posto
            );

            return ['status' => 'success', 'message' => 'Tipo de Atendimento cadastrado com sucesso!'];
        }
        return ['status' => 'error', 'message' => 'Erro ao cadastrar Tipo de Atendimento'];
    }

    public static function buscaPorTipoAtendimento($tipo_atendimento, $posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT tipo_atendimento, codigo, descricao, ativo FROM tbl_tipo_atendimento WHERE tipo_atendimento = {$tipo_atendimento} AND posto = {$posto}";
        $res = pg_query($con, $sql);

        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : ['success' => false, 'error' => 'Tipo de Atendimento não encontrado.'];
    }

    public function atualizar()
    {
        $con = Db::getConnection();
        $usuario = Autenticador::getUsuario();
        $tipo_atendimento   = intval($this->dados['tipo_atendimento']);

        $codigo = pg_escape_string($this->dados['codigo']);
        $descricao = pg_escape_string($this->dados['descricao']);
        $ativo = ($this->dados['ativo'] === 't') ? 't' : 'f';
        $posto = intval($this->posto);

        $sqlCheck = "SELECT 1 FROM tbl_tipo_atendimento WHERE codigo = '{$codigo}' AND tipo_atendimento != {$tipo_atendimento} AND posto = {$posto}";
        $check = pg_query($con, $sqlCheck);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Já existe um tipo de atendimento com esse código.'];
        }

        $sqlAntes = "SELECT codigo, descricao, ativo FROM tbl_tipo_atendimento WHERE tipo_atendimento = $tipo_atendimento AND posto = $posto";
        $resAntes = pg_query($con, $sqlAntes);

        if (pg_num_rows($resAntes) > 0) {
            $codigoAntes = pg_fetch_result($resAntes, 0, 'codigo');
            $descicaoAntes = pg_fetch_result($resAntes, 0, 'descricao');
            $ativoAntes = pg_fetch_result($resAntes, 0, 'ativo');

            $antes = [
                'codigo' => $codigoAntes,
                'descricao'    => $descicaoAntes,
                'ativo' => $ativoAntes
            ];
        }

        $sqlUpdate = "UPDATE tbl_tipo_atendimento SET codigo = '{$codigo}', descricao = '{$descricao}', ativo = '{$ativo}'
                      WHERE tipo_atendimento = {$tipo_atendimento} AND posto = {$posto}";
        $update = pg_query($con, $sqlUpdate);

        if ($update) {
            $depois = [
                'codigo' => $codigo,
                'descricao'    => $descricao,
                'ativo' => $ativo
            ];

            LogAuditor::registrar(
                'tbl_tipo_atendimento',
                $tipo_atendimento,
                'update',
                $antes,
                $depois,
                $usuario,
                $posto
            );

        return ['status' => 'success', 'message' => 'Tipo de Atendimento atualizado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao atualizar Tipo de Atendimento.'];
    }

    public static function listarTodos($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT tipo_atendimento, codigo, descricao, ativo FROM tbl_tipo_atendimento WHERE posto = {$posto} ORDER BY descricao ASC";
        $res = pg_query($con, $sql);

        $lista = [];
        while ($row = pg_fetch_assoc($res)) {
            $lista[] = $row;
        }

        return $lista;
    }

    private static function validaCamposTipoAtendimento($codigo, $descricao)
    {

        $obrigatorios = [
            'Código' => $codigo,
            'Descrição' => $descricao
        ];

        foreach ($obrigatorios as $campo => $valor) {
            if (empty($valor) && $valor !== "0") {
                return "O campo '$campo' é obrigatório!";
            }
        }

        if (strlen($codigo) >= 50) {
            return "O campo código não pode ultrapassar 50 caracteres.";
        }

        if (strlen($descricao) >= 50) {
            return "O nome descrição não pode ultrapassar 50 caracteres.";
        }

        return null;
    }
}
