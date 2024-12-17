<?php

include '../../model/conexao.php';

header('Content-Type: application/json');

$sql = "SELECT cpf, nome, endereco, numero FROM tbl_cliente";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $clientes = array();
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    echo json_encode($clientes);
} else {
    echo json_encode([]);
}

$conn->close();
?>
