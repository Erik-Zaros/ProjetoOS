<?php

namespace App\Controller;

use App\Core\Db;

class AuthController
{
    public static function login($login, $senha)
    {
        if (empty($login) || empty($senha)) {
            return ['success' => false, 'message' => 'Login e senha obrigatórios'];
        }

        $con = Db::getConnection();

        $sql = "SELECT u.usuario, u.login, u.senha, u.ativo AS usuario_ativo,
                       p.posto, p.nome AS posto_nome, p.ativo AS posto_ativo
                FROM tbl_usuario u
                JOIN tbl_posto p ON u.posto = p.posto
                WHERE u.login = $1
                LIMIT 1";

        $res = pg_query_params($con, $sql, [$login]);

        if (pg_num_rows($res) === 1) {
            $row = pg_fetch_assoc($res);

            if (!password_verify($senha, $row['senha'])) {
                return ['success' => false, 'message' => 'Login ou Senha Inválido'];
            }

            if ($row['usuario_ativo'] !== 't') {
                return ['success' => false, 'message' => 'Usuário inativo'];
            }

            if ($row['posto_ativo'] !== 't') {
                return ['success' => false, 'message' => 'Posto inativo'];
            }

            $_SESSION['usuario']     = $row['usuario'];
            $_SESSION['login']       = $row['login'];
            $_SESSION['posto']       = $row['posto'];
            $_SESSION['posto_nome']  = $row['posto_nome'];

            return ['success' => true];
        }

        return ['success' => false, 'message' => 'Usuário não encontrado'];
    }
}
