<?php

include '../../model/conexao.php';

if (isset($_GET['codigo']) && !empty($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    $sql = "SELECT id, codigo, descricao, ativo FROM tbl_produto WHERE codigo = '$codigo'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(['success' => false, 'error' => 'Produto não encontrado.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Código do produto inválido.']);
}

$conn->close();
?>
