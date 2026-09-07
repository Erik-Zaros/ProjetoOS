<?php

namespace App\Controller;

use App\Service\PostoService;

class PostoController
{
    public static function buscar(): ?array
    {
        $service = new PostoService();

        $posto = $service->buscarAtual();

        return $posto?->toArray();
    }

    public static function buscarNome(): ?string
    {
        $service = new PostoService();

        return $service->buscarNome();
    }

    public static function usaModulo(string $modulo): bool
    {
        $service = new PostoService();

        return $service->usaModulo($modulo);
    }

    public static function usaModuloEstoque(): bool
    {
        $service = new PostoService();

        return $service->usaModuloEstoque();
    }

    public static function listar(): array
    {
        $service = new PostoService();

        return $service->listarTodos();
    }
}