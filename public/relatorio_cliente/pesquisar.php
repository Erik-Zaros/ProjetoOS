<?php

require '../../vendor/autoload.php';

use App\Controller\ClienteController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$params = !empty($_GET) ? $_GET : $_POST;
$result = ClienteController::relatorio($params, $posto);
echo json_encode($result);
