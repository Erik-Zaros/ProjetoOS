<?php

include '../../model/conexao.php';

function buscaOS($os) {

    global $conn, $os;

    $os = $_GET['os'];

    $sql = "SELECT tbl_os.os,
                   tbl_os.data_abertura,
                   tbl_os.nome_consumidor,
                   tbl_os.cpf_consumidor,
                   tbl_os.produto_id AS produto_codigo
                FROM tbl_os
                WHERE tbl_os.os = $os";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $row['data_abertura'] = date('Y-m-d', strtotime($row['data_abertura']));
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Ordem de serviço não encontrada."]);
    }
}

echo buscaOS($os);

$conn->close();
?>
