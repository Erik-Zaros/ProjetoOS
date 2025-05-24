<?php

include '../../model/dbconfig.php';
include '../login/autentica_usuario.php';

function cadastraUsuario() {
    global $con, $login_posto;

    $login = $_POST['login'];
    $senha = $_POST['senha'];
    $ativo = (isset($_POST['ativo']) && $_POST['ativo'] === 'on') ? 'true' : 'false';

    $valida_login = "SELECT usuario FROM tbl_usuario WHERE login = '$login' AND posto = $login_posto";
    $result = pg_query($con, $valida_login);

    if (pg_num_rows($result) > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Login já cadastrado!'
        ]);
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO tbl_usuario (login, senha, ativo, posto) VALUES ('$login', '$senha_hash', $ativo, $login_posto)";
        $insert = pg_query($con, $sql);

        if ($insert) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Usuário cadastrado com sucesso!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Erro ao cadastrar usuário: ' . pg_last_error($con)
            ]);
        }
    }
}

cadastraUsuario();
pg_close($con);

?>

