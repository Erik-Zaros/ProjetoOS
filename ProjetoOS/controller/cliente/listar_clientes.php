<?php

include '../../model/conexao.php';

header('Content-Type: application/json');

$sql = "SELECT cpf, nome, cep, endereco, bairro, numero, cidade, estado FROM tbl_cliente";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    echo json_encode($clientes);
} else {
    echo json_encode([]);
}

$conn->close();
?>
