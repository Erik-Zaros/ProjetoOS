<?php
include '../../model/conexao.php';

$sql = "SELECT o.*, c.nome AS nome_cliente, p.descricao AS produto_descricao FROM tbl_os o 
        INNER JOIN tbl_cliente c ON o.cliente_id = c.id
        INNER JOIN tbl_produto p ON o.produto_codigo = p.codigo";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $ordens = [];
    while($row = $result->fetch_assoc()) {
        $finalizada = $row['finalizada'] == 1 ? true : false;
        $ordens[] = [
            'os' => $row['os'],
            'cliente' => $row['nome_cliente'],
            'produto' => $row['produto_descricao'],
            'data_abertura' => $row['data_abertura'],
            'finalizada' => $finalizada
        ];
    }
    echo json_encode($ordens);
} else {
    echo json_encode([]);
}

$conn->close();
?>
