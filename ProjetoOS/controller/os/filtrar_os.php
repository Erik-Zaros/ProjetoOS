<?php
include '../../model/conexao.php';

$numeroOs = $_POST['os'] ?? '';
$nomeCliente = $_POST['nomeCliente'] ?? '';
$dataInicio = $_POST['dataInicio'] ?? '';
$dataFim = $_POST['dataFim'] ?? '';

$sql = "SELECT os, nome_consumidor AS cliente, produto_codigo AS produto, data_abertura FROM tbl_os WHERE 1";

if (!empty($numeroOs)) {
    $sql .= " AND os = '$numeroOs'";
}
if (!empty($nomeCliente)) {
    $sql .= " AND nome_consumidor LIKE '%$nomeCliente%'";
}
if (!empty($dataInicio) && !empty($dataFim)) {
    $sql .= " AND data_abertura BETWEEN '$dataInicio' AND '$dataFim'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $ordens = [];
    while ($row = $result->fetch_assoc()) {
        $ordens[] = [
            'os' => $row['os'],
            'cliente' => $row['cliente'],
            'produto' => $row['produto'],
            'data_abertura' => $row['data_abertura']
        ];
    }
    echo json_encode($ordens);
} else {
    echo json_encode([]);
}

$conn->close();
?>
