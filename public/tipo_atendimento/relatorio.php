<?php

require '../../vendor/autoload.php';

use App\Service\Export\TipoAtendimentoCsvExportService;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
$service = new TipoAtendimentoCsvExportService();
$service->gerar($posto);