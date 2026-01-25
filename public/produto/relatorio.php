<?php

require '../../vendor/autoload.php';

use App\Service\Export\ProdutoCsvExportService;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
$service = new ProdutoCsvExportService();
$service->gerar($posto);