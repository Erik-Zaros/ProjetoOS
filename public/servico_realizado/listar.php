<?php

require '../../vendor/autoload.php';

use App\Controller\ServicoRealizadoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

echo json_encode(ServicoRealizadoController::listar($posto));
