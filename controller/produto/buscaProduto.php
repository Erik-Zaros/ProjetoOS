<?php

include '../../model/dbconfig.php';
include '../login/autentica_usuario.php';

function buscaProduto($codigo) {

    global $con, $codigo, $login_posto;

    if (isset($_GET['codigo']) && !empty($_GET['codigo'])) {
        $codigo = $_GET['codigo'];

        $sql = "SELECT produto, codigo, descricao, ativo FROM tbl_produto WHERE codigo = '$codigo' AND posto = $login_posto";
        $result = pg_query($con, $sql);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                echo json_encode(pg_fetch_assoc($result));
            } else {
                echo json_encode(['success' => false, 'error' => 'Produto não encontrado.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => pg_last_error($con)]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Código do produto inválido.']);
    }
}

echo buscaProduto($codigo);

pg_close($con);
?>
