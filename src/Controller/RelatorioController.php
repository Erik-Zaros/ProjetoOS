<?php

namespace App\Controller;

use App\Core\Db;

class RelatorioController
{
    public static function gerarCSV($posto)
    {
        $con = Db::getConnection();

        $sqlVerifica = "SELECT
            (SELECT COUNT(*) FROM tbl_cliente WHERE posto = $1) AS total_clientes,
            (SELECT COUNT(*) FROM tbl_produto WHERE posto = $1) AS total_produtos,
            (SELECT COUNT(*) FROM tbl_os WHERE posto = $1) AS total_ordens_servico";
        $resVerifica = pg_query_params($con, $sqlVerifica, [$posto]);

        if (!$resVerifica || pg_num_rows($resVerifica) === 0) {
            return ['erro' => true, 'mensagem' => 'Erro na verificação de dados.'];
        }

        $dados = pg_fetch_assoc($resVerifica);
        if ($dados['total_clientes'] == 0 || $dados['total_produtos'] == 0 || $dados['total_ordens_servico'] == 0) {
            return ['erro' => true, 'mensagem' => 'Cadastre pelo menos um cliente, produto e ordem de serviço.'];
        }

        $sql = "SELECT
                  os.os AS ordem_servico,
                  os.data_abertura,
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
            WHERE os.posto = $1
            ORDER BY os.os ASC";

        $res = pg_query_params($con, $sql, [$posto]);

        if (!$res || pg_num_rows($res) === 0) {
            return ['erro' => true, 'mensagem' => 'Nenhum registro encontrado.'];
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
            $data = date('d/m/Y', strtotime($row['data_abertura']));

            fputcsv($output, [
                $row['ordem_servico'], $data, $finalizada,
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
