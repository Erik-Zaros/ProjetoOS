<?php

include '../../model/dbconfig.php';

function editaProduto() {

    global $con;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $codigo = $_POST['codigo'];
        $descricao = $_POST['descricao'];
        $ativo = ($_POST['ativo']) ==  't' ? 'true' : 'false';

        $valida_produto = "SELECT codigo, descricao
                           FROM tbl_produto 
                           WHERE codigo = '$codigo' AND descricao = '$descricao' AND id != '$id'";
        $result = pg_query($con, $valida_produto);

        if (pg_num_rows($result) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Não é possível editar. Já existe um produto com esse código e descrição.']);
        } else {
            $sql = "UPDATE tbl_produto SET codigo = '$codigo', descricao = '$descricao', ativo = '$ativo' WHERE id = '$id'";

            $update = pg_query($con, $sql);

            if ($update) {
                echo json_encode(['status' => 'success', 'message' => 'Produto atualizado com Sucesso.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar o produto: ' . pg_last_error($con)]);
            }
        }
    }
}

editaProduto();

pg_close($con);
?>
