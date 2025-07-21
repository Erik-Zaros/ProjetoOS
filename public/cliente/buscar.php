<?php

require '../../vendor/autoload.php';
session_start();

use App\Controller\ClienteController;

$posto = $_SESSION['login_posto'] ?? 1;
$cpf = $_GET['cpf'] ?? '';

$result = ClienteController::buscar($cpf, $posto);
echo is_array($result) ? json_encode($result) : $result;
