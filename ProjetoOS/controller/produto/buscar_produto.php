<?php
include '../../model/conexao.php';

$codigo = $_GET['codigo'];

$sql = "SELECT codigo, descricao, ativo FROM tbl_produto WHERE codigo='$codigo'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Produto não encontrado']);
}

$conn->close();
?>
