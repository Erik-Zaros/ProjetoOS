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

        $sql = "SELECT usuario FROM tbl_usuario WHERE login = $1 AND posto = $2";
        $res = pg_query_params($con, $sql, [$this->dados['login'], $this->posto]);

        if (pg_num_rows($res) > 0) {
            return ['status' => 'error', 'message' => 'Login já cadastrado!'];
        }

        $senha_hash = password_hash($this->dados['senha'], PASSWORD_DEFAULT);
        $ativo = ($this->dados['ativo'] ?? 'off') === 'on';

        $sql = "INSERT INTO tbl_usuario (login, nome, senha, ativo, posto)
                VALUES ($1, $2, $3, $4, $5)";

        $res = pg_query_params($con, $sql, [
            $this->dados['login'],
            $this->dados['nome'],
            $senha_hash,
            $ativo,
            $this->posto
        ]);

        return $res
            ? ['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao cadastrar usuário.'];
    }

    public static function listar($posto)
    {
        $con = Db::getConnection();

        $sql = "SELECT usuario, login, nome, ativo FROM tbl_usuario
                WHERE posto = $1 ORDER BY usuario ASC";

        $res = pg_query_params($con, $sql, [$posto]);
        $usuarios = [];

        while ($row = pg_fetch_assoc($res)) {
            $usuarios[] = $row;
        }

        return $usuarios;
    }

    public static function buscar($usuarioId, $posto)
    {
        $con = Db::getConnection();

        $sql = "SELECT usuario, login, nome, ativo FROM tbl_usuario
                WHERE usuario = $1 AND posto = $2";

        $res = pg_query_params($con, $sql, [$usuarioId, $posto]);
        return pg_fetch_assoc($res) ?: null;
    }

    public function editar()
    {
        $con = Db::getConnection();

        $ativo = isset($this->dados['ativo']) && $this->dados['ativo'] === 'on' ? 'true' : 'false';

        $sql = "UPDATE tbl_usuario SET login = $1, nome = $2, ativo = $3";
        $params = [
            $this->dados['login'],
            $this->dados['nome'],
            $ativo
        ];

        if (!empty($this->dados['senha'])) {
            $sql .= ", senha = $4 WHERE usuario = $5 AND posto = $6";
            $params[] = password_hash($this->dados['senha'], PASSWORD_DEFAULT);
            $params[] = $this->dados['usuario'];
            $params[] = $this->posto;
        } else {
            $sql .= " WHERE usuario = $4 AND posto = $5";
            $params[] = $this->dados['usuario'];
            $params[] = $this->posto;
        }

        $res = pg_query_params($con, $sql, $params);

        return $res
            ? ['status' => 'success', 'message' => 'Usuário atualizado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao atualizar usuário.'];
    }
}
