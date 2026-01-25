<?php

require '../../vendor/autoload.php';

use App\Service\Export\ServicoRealizadoCsvExportService;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
$service = new ServicoRealizadoCsvExportService();
$service->gerar($posto);