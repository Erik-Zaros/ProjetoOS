<?php

include '../../model/conexao.php';

$conditions = [];
$params = [];
$types = "";

$base_sql = "SELECT 
    tbl_os.os,
    tbl_os.nome_consumidor AS cliente,
    tbl_produto.descricao AS produto,
    tbl_os.data_abertura,
    tbl_os.finalizada 
FROM tbl_os
INNER JOIN tbl_produto ON tbl_os.produto_id = tbl_produto.id
WHERE 1=1";

if (!empty($_POST['os'])) {
    $conditions[] = "tbl_os.os = ?";
    $params[] = $_POST['os'];
    $types .= "i";
}

if (!empty($_POST['nomeCliente'])) {
    $conditions[] = "tbl_os.nome_consumidor LIKE ?";
    $params[] = "%" . $_POST['nomeCliente'] . "%";
    $types .= "s";
}

if (!empty($_POST['dataInicio']) && !empty($_POST['dataFim'])) {
    $conditions[] = "tbl_os.data_abertura BETWEEN ? AND ?";
    $params[] = $_POST['dataInicio'];
    $params[] = $_POST['dataFim'];
    $types .= "ss";
}

$sql = $base_sql;
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

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
