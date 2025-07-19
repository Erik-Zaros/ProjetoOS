<?php

require '../../vendor/autoload.php';

use App\Controller\Relatorio\RelatorioOsController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
RelatorioOsController::gerarCSV($posto);
