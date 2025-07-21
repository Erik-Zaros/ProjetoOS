<?php

require_once '../../vendor/autoload.php';

use App\Controller\AuthController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $res = AuthController::login($login, $senha);
    header('Content-Type: application/json');
    echo json_encode($res);
}
