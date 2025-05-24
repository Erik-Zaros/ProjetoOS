<?php

session_start();
ob_start();

include '../model/dbconfig.php';
include 'login/autentica_usuario.php';

function geraCsv() {

    global $con, $login_posto;

    $query_verifica = "SELECT
            (SELECT COUNT(*) FROM tbl_cliente) AS total_clientes,
            (SELECT COUNT(*) FROM tbl_produto) AS total_produtos,
            (SELECT COUNT(*) FROM tbl_os) AS total_ordens_servico";
    $result_verifica = pg_query($con, $query_verifica);

    if ($result_verifica) {
        $dados = pg_fetch_assoc($result_verifica);

        if ($dados['total_clientes'] == 0 || $dados['total_produtos'] == 0 || $dados['total_ordens_servico'] == 0) {
            $_SESSION['msg'] = "É necessário cadastrar pelo menos um cliente, produto e ordem de serviço para gerar o arquivo.";
            header("Location: ../view/menu.php?alerta=true");
            exit();
        }
    }

    $query = "SELECT
              os.os AS ordem_servico,
              os.data_abertura AS data_abertura,
              os.finalizada AS finalizada,
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
        LEFT JOIN tbl_produto prod ON os.produto_id = prod.produto
        LEFT JOIN tbl_cliente cli ON os.cliente_id = cli.cliente
        LEFT JOIN tbl_posto posto ON posto.posto = os.posto
        WHERE posto.posto = $login_posto
        ORDER BY os.os ASC
    ";

    $result_query = pg_query($con, $query);

    if ($result_query && pg_num_rows($result_query) > 0) {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=Relatorio_Completo_Sistema.csv');

        $resultado = fopen("php://output", 'w');

        $cabecalho = ['OS',
                      'Data Abertura',
                      'Finalizada',
                      'Nome Consumidor',
                      'CPF Consumidor',
                      'CEP Consumidor',
                      'Endereço Consumidor',
                      'Bairro Consumidor',
                      'Número Consumidor',
                      'Cidade Consumidor',
                      'Estado Consumidor',
                      'Código Produto',
                      'Descrição Produto',
                      'Status Produto'];
        fputcsv($resultado, $cabecalho, ';');

        while ($row_query = pg_fetch_assoc($result_query)) {
            $finalizada = $row_query['finalizada'] == 't' ? 'Sim' : 'Não';
            $ativo = $row_query['ativo_produto'] == 't' ? 'Ativo' : 'Inativo';
            $dataFormatada = date('d/m/Y', strtotime($row_query['data_abertura']));

            $linha = [
                $row_query['ordem_servico'],
                $dataFormatada,
                $finalizada,
                $row_query['nome_consumidor'],
                $row_query['cpf_consumidor'],
                $row_query['cep_consumidor'],
                $row_query['endereco_consumidor'],
                $row_query['bairro_consumidor'],
                $row_query['numero_consumidor'],
                $row_query['cidade_consumidor'],
                $row_query['estado_consumidor'],
                $row_query['codigo_produto'],
                $row_query['descricao_produto'],
                $ativo
            ];

            fputcsv($resultado, $linha, ';');
        }

        fclose($resultado);
        exit();
    } else {
        $_SESSION['msg'] = "Erro: Nenhum registro encontrado.";
        header("Location: ../view/menu.php ");
        exit();
    }
}

geraCsv();

pg_close($con);
?>
