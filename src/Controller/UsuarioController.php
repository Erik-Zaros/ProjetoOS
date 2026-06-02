<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Usuario;
use App\Repository\UsuarioRepository;

class UsuarioController
{
    public static function cadastrar($dados, $posto)
    {
        $usuario = new Usuario($dados, $posto);
        return $usuario->salvar();
    }

    public static function editar($dados, $posto)
    {
        $usuario = new Usuario($dados, $posto);
        return $usuario->atualizar();
    }

    public static function listar($posto)
    {
        return UsuarioRepository::listarTodos($posto);
    }

    public static function buscar($usuarioId, $posto)
    {
        return UsuarioRepository::buscarPorId($usuarioId, $posto);
    }
}
