<?php

require '../../vendor/autoload.php';

use App\Controller\Relatorio\RelatorioTipoAtendimentoController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
RelatorioTipoAtendimentoController::gerarCSV($posto);
