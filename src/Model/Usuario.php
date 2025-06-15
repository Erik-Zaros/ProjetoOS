<?php

namespace App\Model;

use App\Core\Db;

class Usuario
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

        $login = pg_escape_string($this->dados['login']);
        $nome = pg_escape_string($this->dados['nome']);
        $senha_hash = pg_escape_string(password_hash($this->dados['senha'], PASSWORD_DEFAULT));
        $ativo = (isset($this->dados['ativo']) && $this->dados['ativo'] === 'on') ? 't' : 'f';
        $posto = intval($this->posto);

        $sqlCheck = "SELECT usuario FROM tbl_usuario WHERE login = '{$login}' AND posto = {$posto}";
        $res = pg_query($con, $sqlCheck);

        if (pg_num_rows($res) > 0) {
            return ['status' => 'error', 'message' => 'Login já cadastrado!'];
        }

        $sqlInsert = "INSERT INTO tbl_usuario (login, nome, senha, ativo, posto)
                      VALUES ('{$login}', '{$nome}', '{$senha_hash}', '{$ativo}', {$posto})";
        $res = pg_query($con, $sqlInsert);

        return $res
            ? ['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao cadastrar usuário.'];
    }

    public static function listar($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT usuario, login, nome, ativo FROM tbl_usuario
                WHERE posto = {$posto} ORDER BY usuario ASC";

        $res = pg_query($con, $sql);
        $usuarios = [];

        while ($row = pg_fetch_assoc($res)) {
            $usuarios[] = $row;
        }

        return $usuarios;
    }

    public static function buscar($usuarioId, $posto)
    {
        $con = Db::getConnection();
        $usuarioId = intval($usuarioId);
        $posto = intval($posto);

        $sql = "SELECT usuario, login, nome, ativo FROM tbl_usuario
                WHERE usuario = {$usuarioId} AND posto = {$posto}";

        $res = pg_query($con, $sql);
        return pg_fetch_assoc($res) ?: null;
    }

    public function editar()
    {
        $con = Db::getConnection();

        $usuarioId = intval($this->dados['usuario']);
        $login = pg_escape_string($this->dados['login']);
        $nome = pg_escape_string($this->dados['nome']);
        $ativo = (isset($this->dados['ativo']) && $this->dados['ativo'] === 'on') ? 't' : 'f';
        $posto = intval($this->posto);

        if (!empty($this->dados['senha'])) {
            $senha_hash = pg_escape_string(password_hash($this->dados['senha'], PASSWORD_DEFAULT));
            $sql = "UPDATE tbl_usuario SET login = '{$login}', nome = '{$nome}', ativo = '{$ativo}', senha = '{$senha_hash}'
                    WHERE usuario = {$usuarioId} AND posto = {$posto}";
        } else {
            $sql = "UPDATE tbl_usuario SET login = '{$login}', nome = '{$nome}', ativo = '{$ativo}'
                    WHERE usuario = {$usuarioId} AND posto = {$posto}";
        }

        $res = pg_query($con, $sql);

        return $res
            ? ['status' => 'success', 'message' => 'Usuário atualizado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao atualizar usuário.'];
    }
}
