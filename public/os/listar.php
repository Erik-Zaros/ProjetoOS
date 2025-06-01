<?php

require '../../vendor/autoload.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controller\OsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$result = OsController::listar($posto);
echo json_encode($result);
