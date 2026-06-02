<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\ListaBasica;
use App\Repository\ListaBasicaRepository;

class ListaBasicaController
{
    public static function buscarProdutos($termo)
    {
        return ListaBasicaRepository::buscarProdutos($termo);
    }

    public static function buscarPecasPorProduto($produtoId)
    {
        return ListaBasicaRepository::buscarPecasPorProduto($produtoId);
    }

    public static function buscarPecas($termo)
    {
        return ListaBasicaRepository::buscarPecas($termo);
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
