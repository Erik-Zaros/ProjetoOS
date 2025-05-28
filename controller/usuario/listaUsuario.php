<?php

include '../../model/dbconfig.php';
include '../login/autentica_usuario.php';

function listaUsuario() {
    global $con, $login_posto;

    $sql = "SELECT usuario, login, nome, ativo FROM tbl_usuario WHERE posto = $login_posto ORDER BY usuario ASC";
    $result = pg_query($con, $sql);

    $usuarios = [];
    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $usuarios[] = $row;
        }
    }
    echo json_encode($usuarios);
}

listaUsuario();
pg_close($con);
?>

