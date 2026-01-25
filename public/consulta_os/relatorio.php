<?php

require '../../vendor/autoload.php';

use App\Service\Export\OsCsvExportService;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();

$service = new OsCsvExportService();
$service->gerar($posto);
