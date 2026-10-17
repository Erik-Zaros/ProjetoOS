<?php

namespace App\Controller;

use App\Model\Cliente;
use App\Repository\ClienteRepository;
use App\Service\ClienteService;
use App\Service\Export\CsvExporter;
use App\Service\Export\ClienteCsvExportService;
use App\Service\Export\RelatorioClienteCsvExportService;

class ClienteController
{
    public static function cadastrar(array $dados): array
    {
        $service = new ClienteService();
        return $service->cadastrar($dados);
    }

    public static function editar(array $dados): array
    {
        $service = new ClienteService();
        return $service->atualizar($dados);
    }

    public static function buscar(string $cpf): ?array
    {
        $service = new ClienteService();
        return $service->buscarPorCpf($cpf);
    }

    public static function listar(): array
    {
        $service = new ClienteService();
        return $service->listarTodos();
    }

    public static function autocomplete($termo)
    {
        $service = new ClienteService();
        return $service->autocompleteClientes($termo);
    }

    public static function relatorio(array $filtros): array
    {
        $service = new ClienteService();
        return $service->relatorio($filtros, false);
    }

    public static function exportarCsv(array $filtros, int $posto): void
    {
        $service = new RelatorioClienteCsvExportService();

        try {
            $linhas = $service->linhas($filtros, $posto);
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            header('Content-Type: text/plain; charset=UTF-8');
            echo $e->getMessage();
            return;
        } catch (\Throwable $e) {
            error_log($e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Não foi possível gerar o arquivo.';
            return;
        }

        (new CsvExporter())->stream('relatorio_cliente.csv', $service->headers(), $linhas);
    }

    public static function exportarListagemCsv(int $posto): void
    {
        $service = new ClienteCsvExportService();

        try {
            if (!$service->temRegistros($posto)) {
                header('Location: ../../view/cliente?alerta=true');
                exit;
            }

            $linhas = $service->linhas($posto);
        } catch (\Throwable $e) {
            error_log($e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Não foi possível gerar o arquivo.';
            return;
        }

        (new CsvExporter())->stream('clientes.csv', $service->headers(), $linhas);
    }
}