<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Peca;
use App\Repository\PecaRepository;

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
        $resultado = PecaRepository::buscarPorCodigo($codigo, $posto);
        return $resultado ?: ['success' => false, 'error' => 'Peça não encontrado.'];
    }

    public static function listar($posto)
    {
        return PecaRepository::listarTodos($posto);
    }

    public static function apagar($dados, $posto)
    {
        return Peca::excluir($dados, $posto);
    }

    public static function autocomplete($termo, $posto)
    {
        return PecaRepository::autocomplete($termo, $posto);
    }
}
