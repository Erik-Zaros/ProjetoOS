<?php

require '../../vendor/autoload.php';

use App\Controller\ClienteController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

echo json_encode(ClienteController::editar($_POST, $posto));
