<?php

require '../../vendor/autoload.php';

use App\Controller\Relatorio\RelatorioClienteController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
RelatorioClienteController::gerarCSV($posto);
