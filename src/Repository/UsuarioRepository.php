<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Db;

class UsuarioRepository
{
    public static function listarTodos($posto): array
    {
        $con = Db::getConnection();
        $posto = (int) $posto;

        $sql = "SELECT usuario, login, nome, ativo, tecnico, master 
                FROM tbl_usuario
                WHERE posto = {$posto} ORDER BY usuario ASC";
        $res = pg_query($con, $sql);
        $usuarios = [];

        while ($row = pg_fetch_assoc($res)) {
            $usuarios[] = $row;
        }

        return $usuarios;
    }

    public static function buscarPorId($usuarioId, $posto): ?array
    {
        $con = Db::getConnection();
        $usuarioId = (int) $usuarioId;
        $posto = (int) $posto;

        $sql = "SELECT usuario, login, nome, ativo, tecnico, master 
                FROM tbl_usuario
                WHERE usuario = {$usuarioId} AND posto = {$posto}";

        $res = pg_query($con, $sql);
        return pg_fetch_assoc($res) ?: null;
    }
}
