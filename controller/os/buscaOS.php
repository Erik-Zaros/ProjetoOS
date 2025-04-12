<?php

include '../../model/dbconfig.php';

function buscaOS() {
    
    global $con;

    if (!isset($_GET['os']) || !is_numeric($_GET['os'])) {
        echo json_encode(["error" => "Número da OS inválido."]);
        exit;
    }

    $os = intval($_GET['os']);

    $sql = "SELECT tbl_os.os,
                   tbl_os.data_abertura,
                   tbl_os.nome_consumidor,
                   tbl_os.cpf_consumidor,
                   tbl_produto.descricao AS produto,
                   tbl_os.finalizada,
                   tbl_os.produto_id
            FROM tbl_os
            INNER JOIN tbl_produto ON tbl_os.produto_id = tbl_produto.id
            WHERE tbl_os.os = $1";

    $result = pg_query_params($con, $sql, [$os]);

    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $row['data_abertura'] = date('Y-m-d', strtotime($row['data_abertura']));
        $row['finalizada'] = $row['finalizada'] === 't';
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Ordem de serviço não encontrada."]);
    }
}

buscaOS();
pg_close($con);
?>
