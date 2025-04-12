<?php

include '../../model/dbconfig.php';

function filtraOS() {
    
    global $con;

    $conditions = [];
    $params = [];

    $base_sql = "SELECT 
                    tbl_os.os,
                    tbl_os.nome_consumidor AS cliente,
                    tbl_os.cpf_consumidor AS cpf,
                    tbl_produto.descricao AS produto,
                    tbl_os.data_abertura,
                    tbl_os.finalizada 
                FROM tbl_os
                INNER JOIN tbl_produto ON tbl_os.produto_id = tbl_produto.id
                WHERE 1=1";

    if (!empty($_POST['os'])) {
        $conditions[] = "tbl_os.os = $" . (count($params) + 1);
        $params[] = $_POST['os'];
    }

    if (!empty($_POST['nomeCliente'])) {
        $conditions[] = "tbl_os.nome_consumidor ILIKE $" . (count($params) + 1);
        $params[] = "%" . $_POST['nomeCliente'] . "%";
    }

    if (!empty($_POST['dataInicio']) && !empty($_POST['dataFim'])) {
        $conditions[] = "tbl_os.data_abertura BETWEEN $" . (count($params) + 1) . " AND $" . (count($params) + 2);
        $params[] = $_POST['dataInicio'];
        $params[] = $_POST['dataFim'];
    }

    $sql = $base_sql;
    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY tbl_os.os ASC";

    $result = pg_query_params($con, $sql, $params);

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

filtraOS();

pg_close($con);
?>
