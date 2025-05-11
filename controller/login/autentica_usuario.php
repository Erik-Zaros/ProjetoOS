<?php
session_start();
include_once __DIR__ . '/../../model/dbconfig.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

$usuario_id = $_SESSION['usuario'];
$login      = $_SESSION['login'];
$posto_id   = $_SESSION['posto'];

$sql = "SELECT u.usuario, u.login, u.ativo AS usuario_ativo,
               p.posto, p.nome AS posto_nome, p.ativo AS posto_ativo
        FROM tbl_usuario u
        JOIN tbl_posto p ON u.posto = p.posto
        WHERE u.usuario = $1 AND p.posto = $2";

$res = pg_query_params($con, $sql, [$usuario_id, $posto_id]);

if (!$res || pg_num_rows($res) !== 1) {
    session_destroy();
    header("Location: ../../login.php");
    exit;
}

$row = pg_fetch_assoc($res);

if ($row['usuario_ativo'] !== 't' || $row['posto_ativo'] !== 't') {
    session_destroy();
    header("Location: ../../login.php");
    exit;
}

global $login_usuario, $login_posto;

$login_usuario    = $row['usuario'];
$login_posto      = $row['posto'];