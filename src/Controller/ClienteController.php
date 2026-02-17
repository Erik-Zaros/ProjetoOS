<?php

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
        $resultado = Cliente::buscarPorCpf($cpf, $posto);
        return $resultado ?: 'Cliente não encontrado';
    }

    public static function listar($posto)
    {
        return ClienteRepository::listarTodos($posto);
    }

    public static function autocomplete($termo, $posto)
    {
        return ClienteRepository::autocompleteClientes($termo, $posto);
    }

    public static function relatorio(array $filtros, $posto)
    {
        $relatorio = new ClienteRepository($filtros, $posto);
        return $relatorio->relatorio($filtros);
    }
}
