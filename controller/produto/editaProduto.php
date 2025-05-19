<?php

include '../../model/dbconfig.php';
include '../login/autentica_usuario.php';

function editaProduto() {

    global $con, $login_posto;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $produto = $_POST['produto'];
        $codigo = $_POST['codigo'];
        $descricao = $_POST['descricao'];
        $ativo = ($_POST['ativo']) ==  't' ? 'true' : 'false';

        $valida_produto = "SELECT codigo, descricao
                           FROM tbl_produto
                           WHERE codigo = '$codigo' AND descricao = '$descricao' AND produto != '$produto' AND posto = $login_posto";
        $result = pg_query($con, $valida_produto);

        if (pg_num_rows($result) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Não é possível editar. Já existe um produto com esse código e descrição.']);
        } else {
            $sql = "UPDATE tbl_produto SET codigo = '$codigo', descricao = '$descricao', ativo = '$ativo' WHERE produto = '$produto' AND posto = $login_posto";

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
