<?php

require '../../vendor/autoload.php';

use App\Controller\MenuController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();

$dados = MenuController::estatisticasPorPosto($posto);

echo json_encode($dados);
