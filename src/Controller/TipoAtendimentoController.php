<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\TipoAtendimento;
use App\Repository\TipoAtendimentoRepository;

class TipoAtendimentoController
{
    public static function cadastrar($dados, $posto)
    {
        $tipo = new TipoAtendimento($dados, $posto);
        return $tipo->salvar();
    }

    public static function editar($dados, $posto)
    {
        $tipo = new TipoAtendimento($dados, $posto);
        return $tipo->atualizar();
    }

    public static function buscar($tipoAtendimento, $posto)
    {
        $resultado = TipoAtendimentoRepository::buscarPorId($tipoAtendimento, $posto);
        return $resultado ?: ['success' => false, 'error' => 'Tipo de Atendimento não encontrado.'];
    }

    public static function listar($posto)
    {
        return TipoAtendimentoRepository::listarTodos($posto);
    }
}
