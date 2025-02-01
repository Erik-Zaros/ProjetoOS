<?php

include '../../model/conexao.php';

function buscaCliente($cpf) {

    global $conn, $cpf;

    $cpf = $_GET['cpf'];
    $sql = "SELECT cpf, nome, cep, endereco, bairro, numero, cidade, estado FROM tbl_cliente WHERE cpf='$cpf'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo "Cliente não encontrado";
    }
}

echo buscaCliente($cpf);

$conn->close();
?>
