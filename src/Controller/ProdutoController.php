<?php
namespace App\Controller;

use App\Model\Produto;

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
        return Produto::buscarPorProduto($produto, $posto);
    }

    public static function listar($posto)
    {
        return Produto::listarTodos($posto);
    }

    public static function apagar($dados, $posto)
    {
        return Produto::excluir($dados, $posto);
    }

    public static function autocomplete($termo, $posto)
    {
        return Produto::autocompleteProdutos($termo, $posto);
    }
}
