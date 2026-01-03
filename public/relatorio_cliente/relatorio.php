<?php

require '../../vendor/autoload.php';

use App\Controller\Relatorio\ClienteRelatorioController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto   = Autenticador::getPosto();
$filtros = $_POST;

ClienteRelatorioController::gerarCSV($filtros, $posto);
