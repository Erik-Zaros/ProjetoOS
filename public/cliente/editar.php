<?php

require '../../vendor/autoload.php';
session_start();

use App\Controller\ClienteController;

$posto = $_SESSION['login_posto'] ?? 1;

echo json_encode(ClienteController::editar($_POST, $posto));
