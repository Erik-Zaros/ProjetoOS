<?php

require '../../vendor/autoload.php';

use App\Controller\ClienteController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$result = ClienteController::relatorio($_POST, $posto);
echo json_encode($result);
