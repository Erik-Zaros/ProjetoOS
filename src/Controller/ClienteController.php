<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Cliente;
use App\Repository\ClienteRepository;
use App\Service\ClienteService;

class ClienteController
{
    public static function cadastrar(array $dados): array
    {
        $service = new ClienteService();
        return $service->cadastrar($dados);
    }

    public static function editar(array $dados): array
    {
        $service = new ClienteService();
        return $service->atualizar($dados);
    }

    public static function buscar(string $cpf): ?array
    {
        $service = new ClienteService();
        return $service->buscarPorCpf($cpf);
    }

    public static function listar(): array
    {
        $service = new ClienteService();
        return $service->listarTodos();
    }

    public static function autocomplete($termo)
    {
        $service = new ClienteService();
        return $service->autocompleteClientes($termo);
    }

    public static function relatorio(array $filtros): array
    {
        $service = new ClienteService();
        return $service->relatorio($filtros);
    }
}
