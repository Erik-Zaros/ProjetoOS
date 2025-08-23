<?php

namespace App\Model;

use App\Core\Db;
use App\Auth\Autenticador;
use App\Model\LogAuditor;

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
        $usuario = Autenticador::getUsuario();

        $codigo    = pg_escape_string($this->dados['codigo']);
        $descricao = pg_escape_string($this->dados['descricao']);
        $ativo     = ($this->dados['ativo'] === 't') ? 't' : 'f';
        $posto     = intval($this->posto);

        $sqlCheck = "SELECT * FROM tbl_peca WHERE codigo = '{$codigo}' AND descricao = '{$descricao}' AND posto = {$posto}";
        $check = pg_query($con, $sqlCheck);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Peça já cadastrado com esse código e descrição!'];
        }

        $sqlInsert = "INSERT INTO tbl_peca (codigo, descricao, ativo, posto)
                      VALUES ('{$codigo}', '{$descricao}', '{$ativo}', {$posto}) RETURNING peca";
        $insert = pg_query($con, $sqlInsert);

        if ($insert && pg_num_rows($insert) > 0) {
            $peca = pg_fetch_result($insert, 0, 'peca');
            $depois = [
                'codigo'    => $codigo,
                'descricao' => $descricao,
                'ativo'     => $ativo
            ];

            $antes = null;

            LogAuditor::registrar(
                'tbl_peca',
                $peca,
                'insert',
                $antes,
                $depois,
                $usuario
            );

            return ['status' => 'success', 'message' => 'Peça cadastrada com sucesso!'];
        }
        return ['status' => 'error', 'message' => 'Erro ao cadastrar peça!'];
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
        $usuario = Autenticador::getUsuario();

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

        $sqlAntes = "SELECT codigo, descricao, ativo FROM tbl_peca WHERE peca = $peca AND posto = $posto";
        $resAntes = pg_query($con, $sqlAntes);

        if (pg_num_rows($resAntes) > 0) {
            $codigoAntes = pg_fetch_result($resAntes, 0, 'codigo');
            $codigoDepois = pg_fetch_result($resAntes, 0, 'descricao');
            $ativoAntes = pg_fetch_result($resAntes, 0, 'ativo');

            $antes = [
                'codigo'    => $codigoAntes,
                'descricao' => $codigoDepois,
                'ativo'     => $ativoAntes
            ];
        }

        $sqlUpdate = "UPDATE tbl_peca SET codigo = '{$codigo}', descricao = '{$descricao}', ativo = '{$ativo}'
                      WHERE peca = {$peca} AND posto = {$posto}";
        $update = pg_query($con, $sqlUpdate);

        if ($update) {
            $depois = [
                'codigo'    => $codigo,
                'descricao' => $descricao,
                'ativo'     => $ativo
            ];

            LogAuditor::registrar(
                'tbl_peca',
                $peca,
                'update',
                $antes,
                $depois,
                $usuario
            );

        return ['status' => 'success', 'message' => 'Peça atualizado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao atualizar peça.'];
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

    public static function autocompletePecas($termo, $posto)
    {
        $con   = Db::getConnection();
        $termo = pg_escape_string($termo);
        $posto = intval($posto);

        $sql = "
            SELECT peca, codigo, descricao
              FROM tbl_peca
             WHERE posto = {$posto}
               AND (descricao ILIKE '%{$termo}%' OR codigo ILIKE '%{$termo}%')
             ORDER BY descricao
             LIMIT 20
        ";

        $res = pg_query($con, $sql);
        $sugestoes = [];

        while ($row = pg_fetch_assoc($res)) {
            $sugestoes[] = [
                'label' => $row['descricao'] . " (" . $row['codigo'] . ")",
                'value' => $row['descricao'],
                'peca'  => $row['peca'],
                'codigo'=> $row['codigo']
            ];
        }
        return $sugestoes;
    }
}
