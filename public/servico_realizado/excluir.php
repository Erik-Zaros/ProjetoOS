<?php

require '../../vendor/autoload.php';

use App\Controller\ServicoRealizadoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$servico_realizado = $_POST['servico_realizado'] ?? 0;

$result = ServicoRealizadoController::apagar($servico_realizado, $posto);
echo json_encode($result);
