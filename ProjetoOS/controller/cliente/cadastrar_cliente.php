<?php

include '../../model/conexao.php';

$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$cep = str_replace('-', '', $_POST['cep']);
$endereco = $_POST['endereco'];
$bairro = $_POST['bairro'];
$numero = $_POST['numero'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];

$valida_cpf = "SELECT cpf FROM tbl_cliente WHERE cpf = '$cpf'";
$result = $conn->query($valida_cpf);

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Cliente já cadastrado com esse CPF!']);
} else {
    $sql = "INSERT INTO tbl_cliente (cpf, nome, cep, endereco, bairro, numero, cidade, estado) 
            VALUES ('$cpf', '$nome', '$cep', '$endereco', '$bairro', '$numero', '$cidade', '$estado')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Cliente Cadastrado com Sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao Cadastrar Cliente: ' . $conn->error]);
    }
}

$conn->close();
?>
