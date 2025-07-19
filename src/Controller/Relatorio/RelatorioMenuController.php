<?php

namespace App\Controller\Relatorio;

use App\Core\Db;

class RelatorioMenuController
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
            header("Location: ../../view/menu.php?alerta=true");
            exit;
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=Relatorio_Completo_Sistema.csv');
        $fp = fopen('php://output', 'w');

        function escreverSecao($fp, $titulo, $cabecalho, $dados)
        {
            fputcsv($fp, []); // linha em branco
            fputcsv($fp, [$titulo]);
            fputcsv($fp, $cabecalho);
            foreach ($dados as $linha) {
                fputcsv($fp, $linha);
            }
        }

        $sql = "
            SELECT os.os, to_char(os.data_abertura, 'DD/MM/YYYY') AS data_abertura, os.finalizada,
                   prod.codigo AS produto_codigo, prod.descricao AS produto_descricao, prod.ativo AS ativo_produto,
                   cli.nome AS cliente_nome, cli.cpf AS cliente_cpf
              FROM tbl_os os
         LEFT JOIN tbl_produto prod ON os.produto = prod.produto
         LEFT JOIN tbl_cliente cli ON os.cliente = cli.cliente
             WHERE os.posto = {$posto}
          ORDER BY os.os ASC
        ";
        $res = pg_query($con, $sql);
        $dados = [];
        while ($row = pg_fetch_assoc($res)) {
            $dados[] = [
                $row['os'], $row['data_abertura'],
                $row['finalizada'] === 't' ? 'Sim' : 'Não',
                $row['cliente_nome'], $row['cliente_cpf'],
                $row['produto_codigo'], $row['produto_descricao'],
                $row['ativo_produto'] === 't' ? 'Ativo' : 'Inativo'
            ];
        }
        $cabecalho = ['OS', 'Data Abertura', 'Finalizada', 'Nome Consumidor', 'CPF Consumidor', 'Código Produto', 'Descrição Produto', 'Status Produto'];
        escreverSecao($fp, 'RELATÓRIO DE ORDENS DE SERVIÇO', $cabecalho, $dados);

        $sql = "SELECT nome, cpf, cep, endereco, bairro, numero, cidade, estado FROM tbl_cliente WHERE posto = {$posto} ORDER BY cliente ASC";
        $res = pg_query($con, $sql);
        $dados = [];
        while ($row = pg_fetch_assoc($res)) {
            $dados[] = [
                $row['nome'], $row['cpf'], $row['cep'], $row['endereco'],
                $row['bairro'], $row['numero'], $row['cidade'], $row['estado']
            ];
        }
        $cabecalho = ['Nome', 'CPF', 'CEP', 'Endereço', 'Bairro', 'Número', 'Cidade', 'Estado'];
        escreverSecao($fp, 'RELATÓRIO DE CLIENTES', $cabecalho, $dados);

        $sql = "SELECT codigo, descricao, ativo, to_char(data_input, 'DD/MM/YYYY') as data_input FROM tbl_produto WHERE posto = {$posto} ORDER BY produto ASC";
        $res = pg_query($con, $sql);
        $dados = [];
        while ($row = pg_fetch_assoc($res)) {
            $dados[] = [
                $row['codigo'], $row['descricao'],
                $row['ativo'] === 't' ? 'Ativo' : 'Inativo',
                $row['data_input']
            ];
        }
        $cabecalho = ['Código', 'Descrição', 'Ativo', 'Data de Cadastro'];
        escreverSecao($fp, 'RELATÓRIO DE PRODUTOS', $cabecalho, $dados);

        $sql = "SELECT codigo, descricao, ativo, to_char(data_input, 'DD/MM/YYYY') as data_input FROM tbl_peca WHERE posto = {$posto} ORDER BY peca ASC";
        $res = pg_query($con, $sql);
        $dados = [];
        while ($row = pg_fetch_assoc($res)) {
            $dados[] = [
                $row['codigo'], $row['descricao'],
                $row['ativo'] === 't' ? 'Ativo' : 'Inativo',
                $row['data_input']
            ];
        }
        $cabecalho = ['Código', 'Descrição', 'Ativo', 'Data de Cadastro'];
        escreverSecao($fp, 'RELATÓRIO DE PEÇAS', $cabecalho, $dados);

        fclose($fp);
        exit;
    }
}
