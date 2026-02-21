<?php
namespace App\Controller;

use App\Model\ServicoRealizado;

class ServicoRealizadoController
{
    public static function cadastrar($dados, $posto)
    {
        $servico_realizado = new ServicoRealizado($dados, $posto);
        return $servico_realizado->salvar();
    }

    public static function editar($dados, $posto)
    {
        $servico_realizado = new ServicoRealizado($dados, $posto);
        return $servico_realizado->atualizar();
    }

    public static function buscar($servico_realizado, $posto)
    {
        return ServicoRealizado::buscarPorServico($servico_realizado, $posto);
    }

    public static function listar($posto)
    {
        return ServicoRealizado::listarTodos($posto);
    }

    public static function apagar($dados, $posto)
    {
        return ServicoRealizado::excluir($dados, $posto);
    }
}
