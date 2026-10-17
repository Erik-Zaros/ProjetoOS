<?php

namespace App\Service\Export;

use App\Core\Db;

class RelatorioClienteCsvExportService
{
    public function headers(): array
    {
        return ['Nome', 'CPF', 'CEP', 'Endereço', 'Bairro', 'Número', 'Cidade', 'Estado', 'Data Cadastro', 'OS'];
    }

    public function linhas(array $filtros, int $posto): iterable
    {
        $dataInicio = $this->normalizarData(trim($filtros['dataInicio'] ?? ''));
        $dataFim    = $this->normalizarData(trim($filtros['dataFim'] ?? ''));
        $cpf        = trim($filtros['cpf'] ?? '');
        $nome       = trim($filtros['nome'] ?? '');
        $somenteComOs = ($filtros['cliente_os'] ?? '') === 'on';

        if ($dataInicio === null || $dataFim === null) {
            throw new \InvalidArgumentException('Período inválido.');
        }

        $params = [$posto, $dataInicio, $dataFim];

        $sql = "
            SELECT c.nome, c.cpf, c.cep, c.endereco, c.bairro, c.numero, c.cidade, c.estado,
                   to_char(c.data_input, 'DD/MM/YYYY') AS data_cadastro,
                   o.oss
            FROM tbl_cliente c
            LEFT JOIN (
                SELECT cliente, string_agg(os::text, ', ' ORDER BY os) AS oss
                FROM tbl_os
                WHERE posto = $1
                GROUP BY cliente
            ) o ON o.cliente = c.cliente
            WHERE c.posto = $1
              AND c.data_input::date BETWEEN $2 AND $3
        ";

        if ($cpf !== '') {
            $params[] = $cpf;
            $sql .= " AND c.cpf ILIKE '%' || $" . count($params) . " || '%'";
        }

        if ($nome !== '') {
            $params[] = $nome;
            $sql .= " AND c.nome ILIKE '%' || $" . count($params) . " || '%'";
        }

        if ($somenteComOs) {
            $sql .= " AND o.oss IS NOT NULL";
        }

        $sql .= " ORDER BY c.nome";

        $res = pg_query_params(Db::getConnection(), $sql, $params);
        if ($res === false) {
            throw new \RuntimeException('Erro ao consultar clientes: ' . pg_last_error(Db::getConnection()));
        }

        return $this->iterar($res);
    }

    private function iterar($res): \Generator
    {
        while ($row = pg_fetch_assoc($res)) {
            yield [
                $row['nome'], $row['cpf'], $row['cep'], $row['endereco'],
                $row['bairro'], $row['numero'], $row['cidade'], $row['estado'],
                $row['data_cadastro'], $row['oss'],
            ];
        }
    }

    // Aceita "2026-08-01" e "01/08/2026" e devolve sempre "Y-m-d" (ou null)
    private function normalizarData(string $d): ?string
    {
        foreach (['Y-m-d', 'd/m/Y'] as $formato) {
            $dt = \DateTime::createFromFormat($formato, $d);
            if ($dt && $dt->format($formato) === $d) {
                return $dt->format('Y-m-d');
            }
        }
        return null;
    }
}