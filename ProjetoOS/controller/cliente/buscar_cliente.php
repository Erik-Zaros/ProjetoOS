<?php

include '../../model/conexao.php';

$cpf = $_GET['cpf'];
$sql = "SELECT * FROM tbl_cliente WHERE cpf='$cpf'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo "Cliente não encontrado";
}

$conn->close();
?>