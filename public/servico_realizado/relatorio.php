<?php

require '../../vendor/autoload.php';

use App\Controller\Relatorio\RelatorioServicoRealizadoController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
RelatorioServicoRealizadoController::gerarCSV($posto);
