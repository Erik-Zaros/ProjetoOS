<?php

include '../../model/conexao.php';

$sql = "SELECT 
    tbl_os.os,
    tbl_os.nome_consumidor AS cliente,
    tbl_produto.descricao AS produto,
    tbl_os.data_abertura,
    tbl_os.finalizada
FROM tbl_os
INNER JOIN tbl_produto ON tbl_os.produto_id = tbl_produto.id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$ordens = [];
while ($row = $result->fetch_assoc()) {
    $dataAberturaFormatada = date('d/m/Y', strtotime($row['data_abertura']));
    $ordens[] = [
        'os' => $row['os'],
        'cliente' => $row['cliente'],
        'produto' => $row['produto'],
        'data_abertura' => $dataAberturaFormatada,
        'finalizada' => $row['finalizada'] == 1
    ];
}

echo json_encode($ordens);

$conn->close();
?>
