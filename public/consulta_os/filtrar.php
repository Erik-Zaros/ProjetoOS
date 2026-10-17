<?php

require '../../vendor/autoload.php';

use App\Controller\OsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$filtros = !empty($_GET) ? $_GET : $_POST;
$result = OsController::filtrar($filtros, $posto);
echo json_encode($result);
