<?php

require '../../vendor/autoload.php';

use App\Controller\PecaController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$codigo = $_GET['codigo'] ?? '';
echo json_encode(PecaController::buscar($codigo, $posto));
