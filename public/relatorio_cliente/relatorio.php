<?php

require '../../vendor/autoload.php';

use App\Service\Export\RelatorioClienteCsvExportService;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto   = Autenticador::getPosto();
$filtros = $_POST;

$service = new RelatorioClienteCsvExportService();
$service->gerar($filtros, $posto);