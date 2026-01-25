<?php
require '../../vendor/autoload.php';

use App\Service\Export\ListaBasicaCsvExportService;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
$produtoId = $_GET['produto'] ?? 0;

if (!$produtoId) {
    echo "Produto não informado.";
    exit;
}

$service = new ListaBasicaCsvExportService();
$service->gerar($produtoId, $posto);
