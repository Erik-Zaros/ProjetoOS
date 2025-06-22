<?php

require '../../vendor/autoload.php';

use App\Controller\OsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$os = $_GET['os'];

$result = OsController::buscarPorNumero($os, $posto);
echo json_encode($result);
