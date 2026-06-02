<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Cliente;
use App\Repository\ClienteRepository;

class ClienteController
{
    public static function cadastrar($dados, $posto)
    {
        $cliente = new Cliente($dados, $posto);
        return $cliente->salvar();
    }

    public static function editar($dados, $posto)
    {
        $cliente = new Cliente($dados, $posto);
        return $cliente->atualizar();
    }

    public static function buscar($cpf, $posto)
    {
        $resultado = new ClienteRepository($posto);
        return $resultado->buscarPorCpf($cpf);
    }

    public static function listar($posto)
    {
        $resultado = new ClienteRepository($posto);
        return $resultado->listarTodos();
    }

    public static function autocomplete($termo, $posto)
    {
        $resultado = new ClienteRepository($posto);
        return $resultado->autocompleteClientes($termo);
    }

    public static function relatorio(array $filtros, $posto)
    {
        $resultado = new ClienteRepository($posto);
        return $resultado->relatorio($filtros);
    }
}
