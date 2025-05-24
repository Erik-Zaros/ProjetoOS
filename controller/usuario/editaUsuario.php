<?php

include '../../model/dbconfig.php';
include '../login/autentica_usuario.php';

function editaUsuario() {
    global $con, $login_posto;

    $usuario_id = intval($_POST['usuario']);
    $login = $_POST['login'];
    $ativo = (isset($_POST['ativo']) && $_POST['ativo'] === 'on') ? 'true' : 'false';
    $senha = $_POST['senha'];

    $senha_sql = "";
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $senha_sql = ", senha = '$senha_hash'";
    }

    $sql = "UPDATE tbl_usuario SET login = '$login', ativo = $ativo $senha_sql WHERE usuario = $usuario_id AND posto = $login_posto";
    $update = pg_query($con, $sql);

    if ($update) {
        echo json_encode(["status" => "success", "message" => "Usuário atualizado com sucesso!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao atualizar usuário: " . pg_last_error($con)]);
    }
}

editaUsuario();
pg_close($con);
?>

