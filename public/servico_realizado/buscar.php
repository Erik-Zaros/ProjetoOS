<?php

require '../../vendor/autoload.php';

use App\Controller\ServicoRealizadoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$servico_realizado = $_GET['servico'];
echo json_encode(ServicoRealizadoController::buscar($servico_realizado, $posto));
