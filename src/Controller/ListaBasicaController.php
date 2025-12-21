<?php

namespace App\Controller;

use App\Model\ListaBasica;

class ListaBasicaController
{
    public static function buscarProdutos($termo)
    {
        return ListaBasica::buscarProdutos($termo);
    }

    public static function buscarPecasPorProduto($produtoId)
    {
        return ListaBasica::buscarPecasPorProduto($produtoId);
    }

    public static function buscarPecas($termo)
    {
        return ListaBasica::buscarPecas($termo);
    }

    public static function adicionarPeca($produtoId, $pecaId, $posto)
    {
        return ListaBasica::adicionarPeca($produtoId, $pecaId, $posto);
    }

    public static function removerPeca($listaBasicaId)
    {
        return ListaBasica::removerPeca($listaBasicaId);
    }
}
