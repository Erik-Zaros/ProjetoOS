<?php

namespace App\Controller;

use App\Model\Cliente;
use App\Repository\ClienteRepository;
use App\Service\ClienteService;
use App\Service\Export\CsvExporter;

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

    public function exportarCsv(array $filtros): void
    {
        $service = new ClienteService();
        $dados = $service->relatorio($filtros, true);

        if (isset($dados['status'])) {
            http_response_code(400);
            header('Content-Type: text/plain; charset=UTF-8');
            echo $dados['message'];
            exit;
        }

        $headers = ['Nome','CPF','CEP','Endereço','Bairro','Número','Cidade','Estado','Data Cadastro','OS'];

        $linhas = array_map(fn($row) => [
            $row['nome'], $row['cpf'], $row['cep'], $row['endereco'],
            $row['numero'], $row['bairro'], $row['cidade'], $row['estado'],
            $row['data_cadastro'], $row['oss'],
        ], $dados);

        $csv = new CsvExporter();
        $csv->stream('relatorio_cliente.csv', $headers, $linhas);
    }
}
