<?php

require '../../vendor/autoload.php';

use App\Controller\PecaController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$peca = $_POST['peca'] ?? 0;

$result = json_encode(PecaController::apagar($peca, $posto));
echo json_encode($result);
