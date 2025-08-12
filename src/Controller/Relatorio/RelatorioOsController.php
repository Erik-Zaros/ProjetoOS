<?php

namespace App\Controller\Relatorio;

use App\Core\Db;

class RelatorioOsController
{
    public static function gerarCSV($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sqlVerifica = "
            SELECT
                (SELECT COUNT(*) FROM tbl_cliente WHERE posto = {$posto}) AS total_clientes,
                (SELECT COUNT(*) FROM tbl_produto WHERE posto = {$posto}) AS total_produtos,
                (SELECT COUNT(*) FROM tbl_os WHERE posto = {$posto}) AS total_ordens_servico
        ";
        $resVerifica = pg_query($con, $sqlVerifica);

        $dados = pg_fetch_assoc($resVerifica);

        if (in_array(0, $dados)) {
            header("Location: ../../view/consulta_os.php?alerta=true");
        }

        $sql = "
            SELECT
                os.os AS ordem_servico,
                to_char(os.data_abertura, 'DD/MM/YYYY') AS data_abertura,
                CASE
                     WHEN os.finalizada IS TRUE THEN 'Finalizado'
                     WHEN os.cancelada IS TRUE THEN 'Cancelada'
                     ELSE 'Em Aberto'
                END AS status_os,
                prod.codigo AS codigo_produto,
                prod.descricao AS descricao_produto,
                CASE
                    WHEN prod.ativo IS TRUE THEN 'Ativo'
                    ELSE 'Inativo'
                END AS status_produto,
                cli.nome AS nome_consumidor,
                cli.cpf AS cpf_consumidor,
                cli.cep AS cep_consumidor,
                cli.endereco AS endereco_consumidor,
                cli.bairro AS bairro_consumidor,
                cli.numero AS numero_consumidor,
                cli.cidade AS cidade_consumidor,
                cli.estado AS estado_consumidor
            FROM tbl_os os
            LEFT JOIN tbl_produto prod ON os.produto = prod.produto
            LEFT JOIN tbl_cliente cli ON os.cliente = cli.cliente
            WHERE os.posto = {$posto}
            ORDER BY os.os ASC
        ";
        $res = pg_query($con, $sql);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=Relatorio_OS.csv');

        $output = fopen('php://output', 'w');

        $cabecalho = [
            'OS', 'Data Abertura', 'Status OS', 'Nome Consumidor', 'CPF Consumidor',
            'CEP Consumidor', 'Endereço Consumidor', 'Bairro Consumidor', 'Número Consumidor',
            'Cidade Consumidor', 'Estado Consumidor', 'Código Produto', 'Descrição Produto', 'Status Produto'
        ];
        fputcsv($output, $cabecalho, ';');

        while ($row = pg_fetch_assoc($res)) {
            $ativo = $row['ativo_produto'] === 't' ? 'Ativo' : 'Inativo';

            fputcsv($output, [
                $row['ordem_servico'], $row['data_abertura'], $row['status_os'],
                $row['nome_consumidor'], $row['cpf_consumidor'],
                $row['cep_consumidor'], $row['endereco_consumidor'], $row['bairro_consumidor'],
                $row['numero_consumidor'], $row['cidade_consumidor'], $row['estado_consumidor'],
                $row['codigo_produto'], $row['descricao_produto'], $row['status_produto']
            ], ';');
        }

        fclose($output);
        exit;
    }
}
