<?php

require '../../vendor/autoload.php';

use App\Controller\Relatorio\RelatorioPecaController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
RelatorioPecaController::gerarCSV($posto);
