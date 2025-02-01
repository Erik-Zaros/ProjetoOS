<?php

include '../../model/conexao.php';

function listaProduto() {

    global $conn;

    $sql = "SELECT id, codigo, descricao, ativo FROM tbl_produto";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $produtos = array();
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }
        echo json_encode($produtos);
    } else {
        echo json_encode([]);
    }
}

listaProduto();

$conn->close();
?>
