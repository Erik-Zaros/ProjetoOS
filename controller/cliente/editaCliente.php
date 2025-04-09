<?php

include '../../model/conexao.php';

function editaCliente() {

    global $conn;

    $cpf = $_POST['cpf'];
    $nome = $_POST['nome'];
    $cep = str_replace('-', '', $_POST['cep']);
    $endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];
    $numero = $_POST['numero'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];

    $sql = "UPDATE tbl_cliente 
            SET nome = '$nome', 
                cep = '$cep', 
                endereco = '$endereco', 
                bairro = '$bairro', 
                numero = '$numero',
                cidade = '$cidade', 
                estado = '$estado'
            WHERE cpf = '$cpf'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Cliente atualizado com sucesso!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao atualizar cliente: " . $conn->error]);
    }
}

editaCliente();

$conn->close();
?>
