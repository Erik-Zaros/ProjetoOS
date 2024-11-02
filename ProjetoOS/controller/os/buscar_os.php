<?php

include '../../model/conexao.php';

$os = $_GET['os'];

$stmt = $conn->prepare("SELECT tbl_os.os,
                               tbl_os.data_abertura,
                               tbl_os.nome_consumidor,
                               tbl_os.cpf_consumidor,
                               tbl_os.produto_id AS produto_codigo
                        FROM tbl_os
                        WHERE tbl_os.os = ?");

$stmt->bind_param("i", $os);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $row['data_abertura'] = date('Y-m-d', strtotime($row['data_abertura']));
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Ordem de serviço não encontrada."]);
}

$stmt->close();
$conn->close();
?>
