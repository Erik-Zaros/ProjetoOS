<?php

require '../../vendor/autoload.php';

use App\Controller\RelatorioController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
RelatorioController::gerarCSV($posto);
