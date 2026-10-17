<?php

namespace App\Service\Export;

use App\Core\Db;

class ClienteCsvExportService
{
    public function headers(): array
    {
        return ['Nome', 'CPF', 'CEP', 'Endereço', 'Bairro', 'Número', 'Cidade', 'Estado', 'Data Cadastro'];
    }

    public function temRegistros(int $posto): bool
    {
        $res = pg_query_params(
            Db::getConnection(),
            'SELECT 1 FROM tbl_cliente WHERE posto = $1 LIMIT 1',
            [$posto]
        );
        return $res !== false && pg_num_rows($res) > 0;
    }

    public function linhas(int $posto): iterable
    {
        $res = pg_query_params(Db::getConnection(), "
            SELECT nome, cpf, cep, endereco, bairro, numero, cidade, estado,
                   to_char(data_input, 'DD/MM/YYYY') AS data_cadastro
            FROM tbl_cliente
            WHERE posto = $1
            ORDER BY nome
        ", [$posto]);

        if ($res === false) {
            throw new \RuntimeException('Erro ao consultar clientes.');
        }

        return $this->iterar($res);
    }

    private function iterar($res): \Generator
    {
        while ($row = pg_fetch_assoc($res)) {
            yield [
                $row['nome'], $row['cpf'], $row['cep'], $row['endereco'],
                $row['bairro'], $row['numero'], $row['cidade'], $row['estado'],
                $row['data_cadastro'],
            ];
        }
    }
}