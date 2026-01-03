<?php

namespace App\Controller\Relatorio;

use App\Core\Db;

class ClienteRelatorioController
{
    public static function gerarCSV(array $filtros, $posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $dataInicio = $filtros['dataInicio'] ?? '';
        $dataFim    = $filtros['dataFim'] ?? '';
        $cpf        = trim($filtros['cpf'] ?? '');
        $nome       = trim($filtros['nome'] ?? '');
        $cliente_os = $filtros['cliente_os'];

        $cond = " AND tbl_cliente.data_input::date BETWEEN '$dataInicio' AND '$dataFim' ";

        if (!empty($cpf)) {
            $cond .= " AND tbl_cliente.cpf ILIKE '%$cpf%' ";
        }

        if (!empty($nome)) {
            $cond .= " AND tbl_cliente.nome ILIKE '%$nome%' ";
        }

        if (isset($cliente_os) && $cliente_os == 'on') {
            $condPri = " AND os_cliente.oss IS NOT NULL ";
        }

        $sql = "
            WITH clientes_filtrados AS (
                SELECT 
                    tbl_cliente.cliente,
                    tbl_cliente.nome,
                    tbl_cliente.cpf,
                    tbl_cliente.cep,
                    tbl_cliente.endereco,
                    tbl_cliente.bairro,
                    tbl_cliente.numero,
                    tbl_cliente.cidade,
                    tbl_cliente.estado,
                    to_char(tbl_cliente.data_input, 'DD/MM/YYYY') AS data_cadastro
                FROM tbl_cliente
                WHERE tbl_cliente.posto = {$posto}
                {$cond}
                AND ('{$cpf}' = '' OR tbl_cliente.cpf ILIKE '%' || '{$cpf}' || '%')
                AND ('{$nome}' = '' OR tbl_cliente.nome ILIKE '%' || '{$nome}' || '%')
            ),
            os_cliente AS (
                SELECT cliente,
                       string_agg(os::text, ', ' ORDER BY os) AS oss
                FROM tbl_os
                WHERE posto = {$posto}
                GROUP BY cliente
            )
            SELECT 
                c.nome,
                c.cpf,
                c.cep,
                c.endereco,
                c.bairro,
                c.numero,
                c.cidade,
                c.estado,
                c.data_cadastro,
                o.oss
            FROM clientes_filtrados c
            LEFT JOIN os_cliente o ON o.cliente = c.cliente
            WHERE 1=1
            {$condPri}
            ORDER BY c.nome
        ";

        $res = pg_query($con, $sql);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=relatorio_cliente.csv');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'Nome',
            'CPF',
            'CEP',
            'Endereço',
            'Bairro',
            'Número',
            'Cidade',
            'Estado',
            'Data Cadastro',
            'OS'
        ], ';');

        while ($row = pg_fetch_assoc($res)) {
            fputcsv($output, [
                $row['nome'],
                $row['cpf'],
                $row['cep'],
                $row['endereco'],
                $row['bairro'],
                $row['numero'],
                $row['cidade'],
                $row['estado'],
                $row['data_cadastro'],
                $row['oss']
            ], ';');
        }

        fclose($output);
        exit;
    }
}
