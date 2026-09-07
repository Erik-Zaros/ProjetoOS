<?php

namespace App\Repository;

use App\Core\Db;
use App\Model\Usuario;
use App\Model\LogAuditor;

class UsuarioRepository
{
    private $posto;
    private $usuario_logado;

    public function __construct($posto, $usuario_logado)
    {
        $this->posto = $posto;
        $this->usuario_logado = $usuario_logado;
    }

    public function inserir(Usuario $usuario): ?int
    {
        $con = Db::getConnection();

        $login          = pg_escape_string($usuario->getLogin());
        $nome           = pg_escape_string($usuario->getNome());
        $senhaHash      = pg_escape_string(password_hash($usuario->getSenha(), PASSWORD_DEFAULT));
        $ativo          = $usuario->isAtivo()   ? 't' : 'f';
        $tecnico        = $usuario->isTecnico() ? 't' : 'f';
        $master         = $usuario->isMaster()  ? 't' : 'f';
        $posto          = $this->posto;
        $usuario_logado = $this->usuario_logado;

        $sql = "INSERT INTO tbl_usuario (login, nome, senha, ativo, tecnico, master, posto)
                VALUES ('{$login}', '{$nome}', '{$senhaHash}', '{$ativo}', '{$tecnico}', '{$master}', {$posto})
                RETURNING usuario";

        $res = pg_query($con, $sql);

        if (!$res || pg_num_rows($res) === 0) {
            return null;
        }

        $novoId = (int) pg_fetch_result($res, 0, 'usuario');

        LogAuditor::registrar(
            'tbl_usuario',
            $novoId,
            'insert',
            null,
            ['login' => $login, 'nome' => $nome, 'ativo' => $ativo, 'tecnico' => $tecnico, 'master' => $master],
            $usuario_logado,
            $posto
        );

        return $novoId;
    }

    public function atualizar(Usuario $usuario): bool
    {
        $con = Db::getConnection();

        $usuarioId      = $usuario->getId();
        $login          = pg_escape_string($usuario->getLogin());
        $nome           = pg_escape_string($usuario->getNome());
        $ativo          = $usuario->isAtivo()   ? 't' : 'f';
        $tecnico        = $usuario->isTecnico() ? 't' : 'f';
        $master         = $usuario->isMaster()  ? 't' : 'f';
        $posto          = $this->posto;
        $usuario_logado = $this->usuario_logado;

        $antes = null;
        $sqlAntes = "SELECT login, nome, ativo, tecnico, master
                     FROM tbl_usuario WHERE usuario = {$usuarioId} AND posto = {$posto}";
        $resAntes = pg_query($con, $sqlAntes);
        if (pg_num_rows($resAntes) > 0) {
            $antes = pg_fetch_assoc($resAntes);
        }

        if ($usuario->getSenha() !== null) {
            $senhaHash = pg_escape_string(password_hash($usuario->getSenha(), PASSWORD_DEFAULT));
            $sql = "UPDATE tbl_usuario
                    SET login = '{$login}', nome = '{$nome}', ativo = '{$ativo}',
                        tecnico = '{$tecnico}', master = '{$master}', senha = '{$senhaHash}'
                    WHERE usuario = {$usuarioId} AND posto = {$posto}";
        } else {
            $sql = "UPDATE tbl_usuario
                    SET login = '{$login}', nome = '{$nome}', ativo = '{$ativo}',
                        tecnico = '{$tecnico}', master = '{$master}'
                    WHERE usuario = {$usuarioId} AND posto = {$posto}";
        }

        $res = pg_query($con, $sql);

        if ($res) {
            LogAuditor::registrar(
                'tbl_usuario',
                $usuarioId,
                'update',
                $antes,
                ['login' => $login, 'nome' => $nome, 'ativo' => $ativo, 'tecnico' => $tecnico, 'master' => $master],
                $usuario_logado,
                $posto
            );
        }

        return (bool) $res;
    }

    public function buscarPorId(int $id): ?Usuario
    {
        $con = Db::getConnection();

        $sql = "SELECT usuario, login, nome, ativo, tecnico, master
                FROM tbl_usuario
                WHERE usuario = {$id} AND posto = {$this->posto}";

        $res = pg_query($con, $sql);

        if (!$res || pg_num_rows($res) === 0) {
            return null;
        }

        return $this->tratar(pg_fetch_assoc($res));
    }

    // uso interno: checagem de login duplicado no cadastro/edição
    public function buscarPorLogin(string $login, ?int $excetoId = null): ?Usuario
    {
        $con = Db::getConnection();
        $login = pg_escape_string($login);

        $sql = "SELECT usuario, login, nome, ativo, tecnico, master
                FROM tbl_usuario
                WHERE login = '{$login}' AND posto = {$this->posto}";

        if ($excetoId !== null) {
            $sql .= " AND usuario <> {$excetoId}";
        }

        $res = pg_query($con, $sql);

        if (!$res || pg_num_rows($res) === 0) {
            return null;
        }

        return $this->tratar(pg_fetch_assoc($res));
    }

    public function listarTodos(): array
    {
        $con = Db::getConnection();

        $sql = "SELECT usuario, login, nome, ativo, tecnico, master
                FROM tbl_usuario
                WHERE posto = {$this->posto}
                ORDER BY usuario ASC";

        $res = pg_query($con, $sql);
        $usuarios = [];

        while ($row = pg_fetch_assoc($res)) {
            $usuarios[] = $this->tratar($row)->toArray();
        }

        return $usuarios;
    }

    private function tratar(array $row): Usuario
    {
        return new Usuario([
            'usuario' => $row['usuario'] ?? null,
            'login'   => $row['login']   ?? '',
            'nome'    => $row['nome']    ?? '',
            'ativo'   => ($row['ativo']   === 't') ? 'on' : null,
            'tecnico' => ($row['tecnico'] === 't') ? 'on' : null,
            'master'  => ($row['master']  === 't') ? 'on' : null,
        ], $this->posto);
    }
}