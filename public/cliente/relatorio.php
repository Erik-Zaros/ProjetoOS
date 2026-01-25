<?php

require '../../vendor/autoload.php';

use App\Service\Export\ClienteCsvExportService;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
$service = new ClienteCsvExportService();
$service->gerar($posto);
