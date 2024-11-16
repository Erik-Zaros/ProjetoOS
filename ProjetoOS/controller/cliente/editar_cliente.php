<?php

include '../../model/conexao.php';

$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$endereco = $_POST['endereco'];
$numero = $_POST['numero'];

$sql = "UPDATE tbl_cliente SET nome='$nome', endereco='$endereco', numero='$numero' WHERE cpf='$cpf'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Cliente Atualizado com Sucesso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro: " . $sql . "<br>" . $conn->error]);
}

$conn->close();
?>
