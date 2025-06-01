<?php

namespace App\Controller;

use App\Model\Usuario;

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
        return $usuario->editar();
    }

    public static function listar($posto)
    {
        return Usuario::listar($posto);
    }

    public static function buscar($usuarioId, $posto)
    {
        return Usuario::buscar($usuarioId, $posto);
    }
}
