<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controller\ClienteController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$controller = new ClienteController();
$controller->exportarCsv($_POST);