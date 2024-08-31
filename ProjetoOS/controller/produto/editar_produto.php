<?php
include '../../model/conexao.php';

$codigo = $_POST['codigo'];
$descricao = $_POST['descricao'];
$ativo = isset($_POST['ativo']) ? 1 : 0;

$sql = "UPDATE tbl_produto SET descricao='$descricao', ativo=$ativo WHERE codigo='$codigo'";

if ($conn->query($sql) === TRUE) {
    echo "Produto atualizado com sucesso!";
} else {
    echo "Erro ao atualizar produto: " . $conn->error;
}

$conn->close();
?>
