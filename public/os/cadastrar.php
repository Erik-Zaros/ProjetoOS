<?php

require '../../vendor/autoload.php';

use App\Controller\OsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$dados = $_POST;

$result = OsController::cadastrar($dados, $posto);
echo json_encode($result);
