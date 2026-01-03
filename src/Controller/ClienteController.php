<?php

namespace App\Controller;

use App\Model\Cliente;
use App\Model\Relatorio\ClienteRelatorio;

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
        return Cliente::listarTodos($posto);
    }

    public static function autocomplete($termo, $posto)
    {
        return Cliente::autocompleteClientes($termo, $posto);
    }

    public static function relatorio(array $filtros, $posto)
    {
        $relatorio = new ClienteRelatorio($filtros, $posto);
        return $relatorio->pesquisar($filtros);
    }
}
