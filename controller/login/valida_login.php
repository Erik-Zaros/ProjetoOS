<?php
session_start();
include_once '../../model/dbconfig.php';

$login = $_POST['login'] ?? '';
$senha = $_POST['senha'] ?? '';


if (empty($login) || empty($senha)) {
  echo json_encode(['success' => false, 'message' => 'Login e senha obrigatórios']);
  exit;
}

$sql = "SELECT u.usuario, u.login, u.senha, u.ativo AS usuario_ativo,
               p.posto, p.nome AS posto_nome, p.ativo AS posto_ativo
        FROM tbl_usuario u
        JOIN tbl_posto p ON u.posto = p.posto
        WHERE u.login = $1
        LIMIT 1";

$res = pg_query_params($con, $sql, [$login]);

if (pg_num_rows($res) === 1) {
  $row = pg_fetch_assoc($res);

  if ($row['senha'] !== $senha) {
    echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
    exit;
  }

  if ($row['usuario_ativo'] !== 't') {
    echo json_encode(['success' => false, 'message' => 'Usuário inativo']);
    exit;
  }

  if ($row['posto_ativo'] !== 't') {
    echo json_encode(['success' => false, 'message' => 'Posto inativo']);
    exit;
  }

  $_SESSION['usuario'] = $row['usuario'];
  $_SESSION['login'] = $row['login'];
  $_SESSION['posto'] = $row['posto'];
  $_SESSION['posto_nome'] = $row['posto_nome'];

  echo json_encode(['success' => true]);
  exit;
}

echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
