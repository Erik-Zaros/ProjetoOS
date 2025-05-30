<?php

include '../../model/dbconfig.php';
include '../login/autentica_usuario.php';

function listaCliente() {

    global $con, $login_posto;

    header('Content-Type: application/json');

    $sql = "SELECT cpf,
                   nome,
                   cep,
                   endereco,
                   bairro,
                   numero,
                   cidade,
                   estado
                FROM tbl_cliente
                WHERE posto = $login_posto
                ORDER BY cpf ASC
            ";
    $result = pg_query($con, $sql);

    if (pg_num_rows($result) > 0) {
        $clientes = [];
        while ($row = pg_fetch_assoc($result)) {
            $clientes[] = $row;
        }
        echo json_encode($clientes);
    } else {
        echo json_encode([]);
    }
}

listaCliente();

pg_close($con);
?>
