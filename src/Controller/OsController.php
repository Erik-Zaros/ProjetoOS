<?php

namespace App\Controller;

use App\Model\Os;

class OsController
{
    public static function cadastrar($dados, $posto)
    {
        $os = new OS($dados, $posto);
        return $os->salvar();
    }

    public static function editar($dados, $posto)
    {
        $os = new Os($dados, $posto);
        return $os->editar();
    }

    public static function buscar($os)
    {
        $resultado = Os::buscar($os);
        return $resultado ?: 'Ordem de Serviço não encontrada';
    }

    public static function listar($posto)
    {
        return Os::listarTodos($posto);
    }

    public static function filtrar(array $filtros, $posto)
    {
        return Os::filtrarOrdens($filtros, $posto);
    }

    public static function finalizar($os, $posto)
    {
        return Os::finalizar($os, $posto);
    }

    public static function cancelar($os, $posto)
    {
        return Os::cancelar($os, $posto);
    }

    public static function buscarPorNumero($os, $posto)
    {
        $resultado = Os::buscarPorNumero($os, $posto);
        return $resultado ?: ['error' => 'Ordem de Serviço não encontrada.'];
    }
}
