<?php

include '../../model/dbconfig.php';

function listaOS() {

    global $con;

    $sql = "SELECT
                tbl_os.os,
                tbl_os.nome_consumidor AS cliente,
                tbl_os.cpf_consumidor AS cpf,
                tbl_produto.descricao AS produto,
                tbl_os.data_abertura,
                tbl_os.finalizada
            FROM tbl_os
            INNER JOIN tbl_produto ON tbl_os.produto_id = tbl_produto.produto
            ORDER BY tbl_os.os ASC
        ";

    $result = pg_query($con, $sql);

    $ordens = [];

    if ($result && pg_num_rows($result) > 0) {
        while ($row = pg_fetch_assoc($result)) {
            $dataFormatada = date('d/m/Y', strtotime($row['data_abertura']));
            $ordens[] = [
                'os'             => $row['os'],
                'cliente'        => $row['cliente'],
                'cpf'            => $row['cpf'],
                'produto'        => $row['produto'],
                'data_abertura'  => $dataFormatada,
                'finalizada'     => $row['finalizada'] === 't'
            ];
        }
    }

    echo json_encode($ordens);
}

listaOS();

pg_close($con);
?>
