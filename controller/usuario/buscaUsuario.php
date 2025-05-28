<?php

include '../../model/dbconfig.php';
include '../login/autentica_usuario.php';

function buscaUsuario($usuario_id) {
    global $con, $login_posto;

    $usuario_id = intval($_GET['usuario']);
    $sql = "SELECT usuario, login, nome, ativo FROM tbl_usuario WHERE usuario = $usuario_id AND posto = $login_posto";
    $result = pg_query($con, $sql);

    if (pg_num_rows($result) > 0) {
        echo json_encode(pg_fetch_assoc($result));
    } else {
        echo "Usuário não encontrado";
    }
}

echo buscaUsuario($_GET['usuario']);
pg_close($con);
?>

