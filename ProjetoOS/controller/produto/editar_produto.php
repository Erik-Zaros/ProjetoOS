<?php

include '../../model/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $descricao = $_POST['descricao'];
    $ativo = ($_POST['ativo']) ? 1 : 0;

    $sql = "UPDATE tbl_produto SET codigo = '$codigo', descricao = '$descricao', ativo = '$ativo' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar o produto: ' . $conn->error]);
    }
}

$conn->close();
?>
