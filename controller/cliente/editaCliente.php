<?php

include '../../model/dbconfig.php';

function editaCliente() {

    global $con;

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

    $update = pg_query($con, $sql);

    if ($update) {
        echo json_encode(["status" => "success", "message" => "Cliente atualizado com sucesso!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao atualizar cliente: " . pg_last_error($con)]);
    }
}

editaCliente();

pg_close($con);
?>
