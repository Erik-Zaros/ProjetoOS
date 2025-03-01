<?php

include '../../model/conexao.php';

function buscaOS() {
    
    global $conn;

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
                   tbl_os.finalizada
            FROM tbl_os
            INNER JOIN tbl_produto ON tbl_os.produto_id = tbl_produto.id
            WHERE tbl_os.os = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $os);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $row['data_abertura'] = date('Y-m-d', strtotime($row['data_abertura']));
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Ordem de serviço não encontrada."]);
    }
}

buscaOS();
$conn->close();
?>
