<?php

include '../../model/conexao.php';

function cadastraProduto() {

    global $conn;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $codigo = $_POST['codigo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $ativo = ($_POST['ativo']) ? 1 : 0;

        if (!empty($codigo) && !empty($descricao)) {
            $valida_produto = "SELECT codigo, descricao FROM tbl_produto WHERE codigo = '$codigo' AND descricao = '$descricao'";
            $result = $conn->query($valida_produto);

            if ($result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Produto já cadastrado com esse código e descrição!']);
            } else {
                $sql = "INSERT INTO tbl_produto (codigo, descricao, ativo) VALUES ('$codigo', '$descricao', $ativo)";

                if ($conn->query($sql) === TRUE) {
                    echo json_encode(['status' => 'success', 'message' => 'Produto Cadastrado com Sucesso!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao Cadastrar Produto: ' . $conn->error]);
                }
            }
        }
    }
}

cadastraProduto();

$conn->close();
?>
