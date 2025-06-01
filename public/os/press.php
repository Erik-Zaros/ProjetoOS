<?php

require '../../vendor/autoload.php';

use App\Controller\OsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$os = isset($_GET['os']) && is_numeric($_GET['os']) ? intval($_GET['os']) : 0;

$result = OsController::buscarPorNumero($os, $posto);
echo json_encode($result);
