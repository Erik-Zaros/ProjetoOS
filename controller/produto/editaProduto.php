<?php

include '../../model/conexao.php';

function editaProduto() {

    global $conn;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $codigo = $_POST['codigo'];
        $descricao = $_POST['descricao'];
        $ativo = ($_POST['ativo']) ? 1 : 0;

        $valida_produto = "SELECT codigo, descricao
                           FROM tbl_produto 
                           WHERE codigo = '$codigo' AND descricao = '$descricao' AND id != '$id'";
        $result = $conn->query($valida_produto);

        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Não é possível editar. Já existe um produto com esse código e descrição.']);
        } else {
            $sql = "UPDATE tbl_produto SET codigo = '$codigo', descricao = '$descricao', ativo = '$ativo' WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(['status' => 'success', 'message' => 'Produto atualizado com Sucesso.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar o produto: ' . $conn->error]);
            }
        }
    }
}

editaProduto();

$conn->close();
?>
