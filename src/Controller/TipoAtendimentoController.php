<?php
namespace App\Controller;

use App\Model\TipoAtendimento;

class TipoAtendimentoController
{
    public static function cadastrar($dados, $posto)
    {
        $tipo_atendimento = new TipoAtendimento($dados, $posto);
        return $tipo_atendimento->salvar();
    }

    public static function editar($dados, $posto)
    {
        $tipo_atendimento = new TipoAtendimento($dados, $posto);
        return $tipo_atendimento->atualizar();
    }

    public static function buscar($tipo_atendimento, $posto)
    {
        return TipoAtendimento::buscaPorTipoAtendimento($tipo_atendimento, $posto);
    }

    public static function listar($posto)
    {
        return TipoAtendimento::listarTodos($posto);
    }
}
