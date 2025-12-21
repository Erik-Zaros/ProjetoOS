<?php

require '../../vendor/autoload.php';

use App\Controller\ServicoRealizadoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$descricao = $_GET['descricao'] ?? '';
echo json_encode(ServicoRealizadoController::buscar($descricao, $posto));
