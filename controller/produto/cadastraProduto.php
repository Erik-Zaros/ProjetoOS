<?php

include '../../model/dbconfig.php';

function cadastraProduto() {

    global $con;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $codigo = $_POST['codigo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $ativo = ($_POST['ativo']) ? 'true' : 'false';

        if (!empty($codigo) && !empty($descricao)) {
            $valida_produto = "SELECT codigo, descricao FROM tbl_produto WHERE codigo = '$codigo' AND descricao = '$descricao'";
            $result = pg_query($con, $valida_produto);

            if (pg_num_rows($result) > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Produto já cadastrado com esse código e descrição!']);
            } else {
                $sql = "INSERT INTO tbl_produto (codigo, descricao, ativo) VALUES ('$codigo', '$descricao', $ativo)";

                $insert = pg_query($con, $sql);

                if ($insert) {
                    echo json_encode(['status' => 'success', 'message' => 'Produto Cadastrado com Sucesso!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao Cadastrar Produto: ' . pg_last_error($con)]);
                }
            }
        }
    }
}

cadastraProduto();

pg_close($con);
?>
