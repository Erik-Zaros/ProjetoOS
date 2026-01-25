<?php

require '../../vendor/autoload.php';

use App\Service\Export\PecaCsvExportService;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
$service = new PecaCsvExportService();
$service->gerar($posto);