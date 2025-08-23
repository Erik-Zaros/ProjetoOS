<?php
require '../../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Controller\EstoqueController;

header('Content-Type: application/json; charset=utf-8');

Autenticador::iniciar();
$posto = Autenticador::getPosto();

echo json_encode(EstoqueController::lancar($_POST, $posto));
