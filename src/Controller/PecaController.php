<?php
namespace App\Controller;

use App\Model\Peca;

class PecaController
{
    public static function cadastrar($dados, $posto)
    {
        $peca = new Peca($dados, $posto);
        return $peca->salvar();
    }

    public static function editar($dados, $posto)
    {
        $peca = new Peca($dados, $posto);
        return $peca->atualizar();
    }

    public static function buscar($codigo, $posto)
    {
        return Peca::buscarPorCodigo($codigo, $posto);
    }

    public static function listar($posto)
    {
        return Peca::listarTodos($posto);
    }

    public static function apagar($dados, $posto)
    {
        return Peca::excluir($dados, $posto);
    }

    public static function autocomplete($termo, $posto)
    {
        return Peca::autocompletePecas($termo, $posto);
    }
}
