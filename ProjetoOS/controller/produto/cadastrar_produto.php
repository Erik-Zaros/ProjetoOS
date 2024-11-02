<?php

include '../../model/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $ativo = ($_POST['ativo']) ? 1 : 0;

    if (!empty($codigo) && !empty($descricao)) {
        $sql = "INSERT INTO tbl_produto (codigo, descricao, ativo) VALUES ('$codigo', '$descricao', $ativo)";

        if ($conn->query($sql) === TRUE) {
            echo "Produto cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar produto: " . $conn->error;
        }
    } else {
        echo "Erro: Código e Descrição são obrigatórios.";
    }
} else {
    echo "Método de requisição inválido.";
}

$conn->close();
?>
