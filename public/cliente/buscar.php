<?php

require '../../vendor/autoload.php';
session_start();

use App\Controller\ClienteController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$cpf = $_GET['cpf'] ?? '';

$result = ClienteController::buscar($cpf, $posto);
echo is_array($result) ? json_encode($result) : $result;
