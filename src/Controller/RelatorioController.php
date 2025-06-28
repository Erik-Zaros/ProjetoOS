<?php

namespace App\Controller;

use App\Core\Db;

class RelatorioController
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

        if (!$resVerifica || pg_num_rows($resVerifica) === 0) {
            echo 'Erro na verificação de dados.'; exit;
        }

        $dados = pg_fetch_assoc($resVerifica);
        if (
            $dados['total_clientes'] == 0 ||
            $dados['total_produtos'] == 0 ||
            $dados['total_ordens_servico'] == 0
        ) {
            echo 'Cadastre pelo menos um cliente, produto e ordem de serviço.'; exit;
        }

        $sql = "
            SELECT
                os.os AS ordem_servico,
                to_char(os.data_abertura, 'DD/MM/YYYY') AS data_abertura, 
                os.finalizada,
                prod.codigo AS codigo_produto,
                prod.descricao AS descricao_produto,
                prod.ativo AS ativo_produto,
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

        if (!$res || pg_num_rows($res) === 0) {
            echo 'Nenhum registro encontrado.'; exit;
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=Relatorio_Completo_Sistema.csv');

        $output = fopen('php://output', 'w');

        $cabecalho = [
            'OS', 'Data Abertura', 'Finalizada', 'Nome Consumidor', 'CPF Consumidor',
            'CEP Consumidor', 'Endereço Consumidor', 'Bairro Consumidor', 'Número Consumidor',
            'Cidade Consumidor', 'Estado Consumidor', 'Código Produto', 'Descrição Produto', 'Status Produto'
        ];
        fputcsv($output, $cabecalho, ';');

        while ($row = pg_fetch_assoc($res)) {
            $finalizada = $row['finalizada'] === 't' ? 'Sim' : 'Não';
            $ativo = $row['ativo_produto'] === 't' ? 'Ativo' : 'Inativo';

            fputcsv($output, [
                $row['ordem_servico'], $row['data_abertura'], $finalizada,
                $row['nome_consumidor'], $row['cpf_consumidor'],
                $row['cep_consumidor'], $row['endereco_consumidor'], $row['bairro_consumidor'],
                $row['numero_consumidor'], $row['cidade_consumidor'], $row['estado_consumidor'],
                $row['codigo_produto'], $row['descricao_produto'], $ativo
            ], ';');
        }

        fclose($output);
        exit;
    }
}
