<?php

include '../../model/dbconfig.php';
header('Content-Type: application/json');

$os = isset($_GET['os']) && is_numeric($_GET['os']) ? intval($_GET['os']) : 0;

$sql = "SELECT
            tbl_os.os,
            tbl_os.data_abertura,
            tbl_os.nome_consumidor,
            tbl_os.cpf_consumidor,
            tbl_produto.descricao AS produto,
            tbl_os.finalizada
        FROM tbl_os
        INNER JOIN tbl_produto ON tbl_os.produto_id = tbl_produto.produto
        WHERE tbl_os.os = $os
    ";

$result = pg_query($con, $sql);
$ordem = pg_fetch_assoc($result);

if (!$ordem) {
    echo json_encode(["error" => "Ordem de Serviço não encontrada."]);
    exit;
}

echo json_encode($ordem);

pg_close($con);

?>
