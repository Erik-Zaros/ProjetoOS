<?php
require '../../vendor/autoload.php';

use App\Controller\Relatorio\RelatorioListaBasicaController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
$produtoId = $_GET['produto'] ?? 0;

if (!$produtoId) {
    echo "Produto não informado.";
    exit;
}

RelatorioListaBasicaController::gerarCSV($produtoId, $posto);
