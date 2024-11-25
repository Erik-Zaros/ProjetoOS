<?php
session_start();
ob_start();

include '../model/conexao.php';

$query_verifica = "SELECT 
        (SELECT COUNT(*) FROM tbl_cliente) AS total_clientes,
        (SELECT COUNT(*) FROM tbl_produto) AS total_produtos,
        (SELECT COUNT(*) FROM tbl_os) AS total_ordens_servico";
$result_verifica = $conn->query($query_verifica);

if ($result_verifica) {
    $dados = $result_verifica->fetch_assoc();

    if ($dados['total_clientes'] == 0 || $dados['total_produtos'] == 0 || $dados['total_ordens_servico'] == 0) {
        $_SESSION['msg'] = "É necessário cadastrar pelo menos um cliente, produto e ordem de serviço para gerar o arquivo.";
        header("Location: ../view/menu.html?alerta=true");
        exit();
    }
}

$query = "SELECT
    tbl_os.os AS ordem_servico, 
    tbl_os.data_abertura AS data_abertura, 
    tbl_os.finalizada AS finalizada,
    tbl_produto.codigo AS codigo_produto, 
    tbl_produto.descricao AS descricao_produto, 
    tbl_produto.ativo AS ativo_produto,
    tbl_cliente.nome AS nome_consumidor,
    tbl_cliente.cpf AS cpf_consumidor
FROM tbl_os
LEFT JOIN tbl_produto ON tbl_os.produto_id = tbl_produto.id
LEFT JOIN tbl_cliente ON tbl_os.cliente_id = tbl_cliente.id
ORDER BY tbl_os.os ASC";

$result_query = $conn->query($query);

if ($result_query && $result_query->num_rows > 0) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=Relatorio_Completo.csv');

    $resultado = fopen("php://output", 'w');

    $cabecalho = ['OS', 'Data Abertura', 'Finalizada', 'Nome Consumidor', 'CPF Consumidor', 'Código Produto', 'Descrição Produto', 'Ativo Produto'];
    fputcsv($resultado, $cabecalho, ';');

    while ($row_query = $result_query->fetch_assoc()) {
        $finalizada = $row_query['finalizada'] ? 'Sim' : 'Não';
        $ativo = $row_query['ativo_produto'] ? 'Sim' : 'Não';

        $linha = [
            $row_query['ordem_servico'],
            $row_query['data_abertura'],
            $finalizada,
            $row_query['nome_consumidor'],
            $row_query['cpf_consumidor'],
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
    header("Location: menu.html");
    exit();
}

$conn->close();
?>
