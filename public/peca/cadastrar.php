<?php

require '../../vendor/autoload.php';

use App\Controller\PecaController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

header('Content-Type: application/json');
echo json_encode(PecaController::cadastrar($_POST, $posto));
