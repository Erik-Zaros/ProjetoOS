<?php

require '../../vendor/autoload.php';

use App\Controller\Relatorio\RelatorioProdutoController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
RelatorioProdutoController::gerarCSV($posto);
