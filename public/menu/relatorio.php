<?php

require '../../vendor/autoload.php';

use App\Controller\Relatorio\RelatorioMenuController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
RelatorioMenuController::gerarCSV($posto);
