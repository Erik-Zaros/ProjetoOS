<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controller\ClienteController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = (int) Autenticador::getPosto();

ClienteController::exportarCsv($_REQUEST, $posto);