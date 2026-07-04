<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Os;
use App\Repository\OsRepository;

class OsController
{
    public static function cadastrar($dados, $posto)
    {
        $os = new Os($dados, $posto);
        return $os->salvar();
    }

    public static function editar($dados, $posto)
    {
        $os = new Os($dados, $posto);
        return $os->editar();
    }

    public static function buscar($os, $posto)
    {
        $resultado = new OsRepository($posto);
        return $resultado->buscarPorId($os);
    }

    public static function listar($posto)
    {
        $resultado = new OsRepository($posto);
        return $resultado->listarTodos();
    }

    public static function filtrar(array $filtros, $posto)
    {
        $resultado = new OsRepository($posto);
        return $resultado->filtrarOrdens($filtros);
    }

    public static function finalizar($os, $posto)
    {
        $resultado = new OsRepository($posto);
        return $resultado->finalizar($os);
    }

    public static function cancelar($os, $posto)
    {
        $resultado = new OsRepository($posto);
        return $resultado->cancelar($os);
    }

    public static function osPress($os, $posto)
    {
        $resultado = new OsRepository($posto);
        $resultado = $resultado->os_press($os);
        return $resultado ?: ['error' => 'Ordem de Serviço não encontrada.'];
    }
}
