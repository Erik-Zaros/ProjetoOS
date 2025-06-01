<?php

require '../../vendor/autoload.php';

use App\Controller\OsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$os = $_POST['os'] ?? 0;

$result = OsController::finalizar($os, $posto);
echo json_encode($result);
