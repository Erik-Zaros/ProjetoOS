<?php

namespace App\Model;

use App\Core\Db;
use App\Auth\Autenticador;
use App\Model\LogAuditor;

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
        $usuario = Autenticador::getUsuario();

        $login = pg_escape_string($this->dados['login']);
        $nome = pg_escape_string($this->dados['nome']);
        $senha_hash = pg_escape_string(password_hash($this->dados['senha'], PASSWORD_DEFAULT));
        $ativo = (isset($this->dados['ativo']) && $this->dados['ativo'] === 'on') ? 't' : 'f';
        $tecnico = (isset($this->dados['tecnico']) && $this->dados['tecnico'] === 'on') ? 't' : 'f';
        $master = (isset($this->dados['master']) && $this->dados['master'] === 'on') ? 't' : 'f';
        $posto = intval($this->posto);

        $sqlCheck = "SELECT usuario FROM tbl_usuario WHERE login = '{$login}' AND posto = {$posto}";
        $res = pg_query($con, $sqlCheck);

        if (pg_num_rows($res) > 0) {
            return ['status' => 'error', 'message' => 'Login já cadastrado!'];
        }

        $sqlInsert = "INSERT INTO tbl_usuario (login, nome, senha, ativo, tecnico, master, posto)
                      VALUES ('{$login}', '{$nome}', '{$senha_hash}', '{$ativo}', '{$tecnico}', '{$master}', {$posto}) RETURNING usuario";
        $res = pg_query($con, $sqlInsert);

        if (pg_num_rows($res) > 0) {
            $usuario_id = pg_fetch_result($res, 0, 'usuario');
            $depois = [
                'login'    => $login,
                'nome' => $nome,
                'ativo'     => $ativo,
                'tecnico' => $tecnico,
                'master' => $master
            ];

            $antes = null;

            LogAuditor::registrar(
                'tbl_usuario',
                $usuario_id,
                'insert',
                $antes,
                $depois,
                $usuario,
                $posto
            );

            return ['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao cadastrar usuário.'];
    }

    public static function listar($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT usuario, login, nome, ativo, tecnico, master FROM tbl_usuario
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

        $sql = "SELECT usuario, login, nome, ativo, tecnico, master FROM tbl_usuario
                WHERE usuario = {$usuarioId} AND posto = {$posto}";

        $res = pg_query($con, $sql);
        return pg_fetch_assoc($res) ?: null;
    }

    public function atualizar()
    {
        $con = Db::getConnection();
        $usuario = Autenticador::getUsuario();

        $usuarioId = intval($this->dados['usuario']);
        $login = pg_escape_string($this->dados['login']);
        $nome = pg_escape_string($this->dados['nome']);
        $ativo = (isset($this->dados['ativo']) && $this->dados['ativo'] === 'on') ? 't' : 'f';
        $tecnico = (isset($this->dados['tecnico']) && $this->dados['tecnico'] === 'on') ? 't' : 'f';
        $master = (isset($this->dados['master']) && $this->dados['master'] === 'on') ? 't' : 'f';
        $posto = intval($this->posto);

        $sqlCheck = "SELECT usuario FROM tbl_usuario WHERE login = '{$login}' AND posto = {$posto} AND usuario <> $usuarioId";
        $res = pg_query($con, $sqlCheck);

        if (pg_num_rows($res) > 0) {
            return ['status' => 'error', 'message' => 'Login já cadastrado!'];
        }

        $sqlAntes = "SELECT login, nome, ativo, tecnico, master FROM tbl_usuario WHERE usuario = $usuarioId AND posto = $posto";
        $resAntes = pg_query($con, $sqlAntes);

        if (pg_num_rows($resAntes) > 0) {
            $loginAntes = pg_fetch_result($resAntes, 0, 'login');
            $nomeAntes = pg_fetch_result($resAntes, 0, 'nome');
            $ativoAntes = pg_fetch_result($resAntes, 0, 'ativo');
            $tecnicoAntes = pg_fetch_result($resAntes, 0, 'tecnico');
            $masterAntes = pg_fetch_result($resAntes, 0, 'master');

            $antes = [
                'login'    => $loginAntes,
                'nome' => $nomeAntes,
                'ativo'     => $ativoAntes,
                'tecnico' => $tecnicoAntes,
                'master' => $masterAntes
            ];
        }

        if (!empty($this->dados['senha'])) {
            $senha_hash = pg_escape_string(password_hash($this->dados['senha'], PASSWORD_DEFAULT));
            $sql = "UPDATE tbl_usuario SET login = '{$login}', nome = '{$nome}', ativo = '{$ativo}', tecnico = '{$tecnico}', master = '{$master}',senha = '{$senha_hash}'
                    WHERE usuario = {$usuarioId} AND posto = {$posto}";
        } else {
            $sql = "UPDATE tbl_usuario SET login = '{$login}', nome = '{$nome}', ativo = '{$ativo}', tecnico = '{$tecnico}', master = '{$master}'
                    WHERE usuario = {$usuarioId} AND posto = {$posto}";
        }

        $res = pg_query($con, $sql);

        if ($res) {
            $depois = [
                'login'    => $login,
                'nome' => $nome,
                'ativo'     => $ativo,
                'tecnico' => $tecnico,
                'master' => $master
            ];

            LogAuditor::registrar(
                'tbl_usuario',
                $usuarioId,
                'update',
                $antes,
                $depois,
                $usuario,
                $posto
            );

        return ['status' => 'success', 'message' => 'Usuário atualizado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao atualizar usuário.'];
    }
}
