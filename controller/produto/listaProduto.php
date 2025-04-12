<?php

include '../../model/dbconfig.php';

function listaProduto() {

    global $con;

    $sql = "SELECT id, codigo, descricao, ativo FROM tbl_produto";

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
