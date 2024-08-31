<?php
include '../../model/conexao.php';

$os = $_GET['os'];

$sql = "SELECT os, data_abertura, nome_consumidor, cpf_consumidor, produto_codigo
        FROM tbl_os 
        WHERE os = '$os'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo "Ordem de serviço não encontrada";
}

$conn->close();
?>
