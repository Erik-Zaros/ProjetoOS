<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Produto;
use App\Repository\ProdutoRepository;

class ProdutoController
{
    public static function cadastrar($dados, $posto)
    {
        $produto = new Produto($dados, $posto);
        return $produto->salvar();
    }

    public static function editar($dados, $posto)
    {
        $produto = new Produto($dados, $posto);
        return $produto->atualizar();
    }

    public static function buscar($produto, $posto)
    {
        $resultado = ProdutoRepository::buscarPorId($produto, $posto);
        return $resultado ?: ['success' => false, 'error' => 'Produto não encontrado.'];
    }

    public static function listar($posto)
    {
        return ProdutoRepository::listarTodos($posto);
    }

    public static function apagar($dados, $posto)
    {
        return Produto::excluir($dados, $posto);
    }

    public static function autocomplete($termo, $posto)
    {
        return ProdutoRepository::autocomplete($termo, $posto);
    }
}
