<?php

include '../../model/dbconfig.php';
include '../login/autentica_usuario.php';

function listaProduto() {

    global $con, $login_posto;

    $sql = "SELECT produto,
                   codigo,
                   descricao,
                   ativo
                FROM tbl_produto
                WHERE posto = $login_posto
                ORDER BY codigo ASC
            ";

    $result = pg_query($con, $sql);

    if (pg_num_rows($result) > 0) {
        $produtos = array();
        while ($row = pg_fetch_assoc($result)) {
            $produtos[] = $row;
        }
        echo json_encode($produtos);
    } else {
        echo json_encode([]);
    }
}

listaProduto();

pg_close($con);
?>
