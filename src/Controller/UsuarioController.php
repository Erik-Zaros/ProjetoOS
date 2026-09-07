<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UsuarioService;

class UsuarioController
{
    public static function cadastrar(array $dados): array
    {
        $service = new UsuarioService();
        return $service->cadastrar($dados);
    }

    public static function editar(array $dados): array
    {
        $service = new UsuarioService();
        return $service->atualizar($dados);
    }

    public static function buscar(int $usuarioId): ?array
    {
        $service = new UsuarioService();
        return $service->buscarPorId($usuarioId);
    }

    public static function listar(): array
    {
        $service = new UsuarioService();
        return $service->listarTodos();
    }
}