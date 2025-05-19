<?php

include '../../model/dbconfig.php';
include '../login/autentica_usuario.php';

function buscaCliente($cpf) {

    global $con, $login_posto;

    $cpf = $_GET['cpf'];
    $sql = "SELECT cpf,
                   nome,
                   cep,
                   endereco,
                   bairro,
                   numero,
                   cidade,
                   estado
                FROM tbl_cliente
                WHERE cpf = '$cpf'
                AND posto = $login_posto
            ";
    $result = pg_query($con, $sql);

    if (pg_num_rows($result) > 0) {
        echo json_encode(pg_fetch_assoc($result));
    } else {
        echo "Cliente não encontrado";
    }
}

echo buscaCliente($_GET['cpf']);

pg_close($con);
?>
