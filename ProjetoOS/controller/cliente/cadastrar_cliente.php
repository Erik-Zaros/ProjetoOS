<?php

include '../../model/conexao.php';

$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$endereco = $_POST['endereco'];
$numero = $_POST['numero'];

$valida_cpf = "SELECT * FROM tbl_cliente WHERE cpf = '$cpf'";
$result = $conn->query($valida_cpf);

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Cliente já cadastrado com esse CPF!']);
} else {
    $sql = "INSERT INTO tbl_cliente (cpf, nome, endereco, numero) VALUES ('$cpf', '$nome', '$endereco', '$numero')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente Cadastrado com Sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar cliente: ' . $conn->error]);
    }
}

$conn->close();
?>
