<?php

require '../../vendor/autoload.php';

use App\Controller\ServicoRealizadoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

header('Content-Type: application/json');
echo json_encode(ServicoRealizadoController::cadastrar($_POST, $posto));
