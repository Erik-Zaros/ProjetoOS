<?php

require '../../vendor/autoload.php';

use App\Controller\ClienteController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

header('Content-Type: application/json');
echo json_encode(ClienteController::listar($posto));
