<?php

include '../../model/conexao.php';

$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$endereco = $_POST['endereco'];
$numero = $_POST['numero'];

$sql = "INSERT INTO tbl_cliente (cpf, nome, endereco, numero) VALUES ('$cpf', '$nome', '$endereco', '$numero')";

if ($conn->query($sql) === TRUE) {
    echo "Cliente cadastrado com sucesso";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
