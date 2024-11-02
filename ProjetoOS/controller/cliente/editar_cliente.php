<?php

include '../../model/conexao.php';

$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$endereco = $_POST['endereco'];
$numero = $_POST['numero'];

$sql = "UPDATE tbl_cliente SET nome='$nome', endereco='$endereco', numero='$numero' WHERE cpf='$cpf'";

if ($conn->query($sql) === TRUE) {
    echo "Cliente atualizado com sucesso";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
