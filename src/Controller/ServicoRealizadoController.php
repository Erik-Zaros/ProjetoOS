<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\ServicoRealizado;
use App\Repository\ServicoRealizadoRepository;

class ServicoRealizadoController
{
    public static function cadastrar($dados, $posto)
    {
        $servico = new ServicoRealizado($dados, $posto);
        return $servico->salvar();
    }

    public static function editar($dados, $posto)
    {
        $servico = new ServicoRealizado($dados, $posto);
        return $servico->atualizar();
    }

    public static function buscar($servicoRealizado, $posto)
    {
        $resultado = ServicoRealizadoRepository::buscarPorId($servicoRealizado, $posto);
        return $resultado ?: ['success' => false, 'error' => 'Serviço Realizado não encontrado.'];
    }

    public static function listar($posto)
    {
        return ServicoRealizadoRepository::listarTodos($posto);
    }

    public static function listarAtivos($posto)
    {
        return ServicoRealizadoRepository::listarAtivos($posto);
    }

    public static function apagar($dados, $posto)
    {
        return ServicoRealizado::excluir($dados, $posto);
    }
}
