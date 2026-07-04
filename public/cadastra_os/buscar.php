<?php

require '../../vendor/autoload.php';

use App\Controller\OsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$os = $_GET['os'] ?? 0;

$result = OsController::buscar($os, $posto);
echo is_array($result) ? json_encode($result) : json_encode(['status' => 'error', 'message' => $result]);
