<?php
require '../../vendor/autoload.php';

use App\Controller\ListaBasicaController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$produtoId = $_GET['produto'] ?? 0;

$result = ListaBasicaController::buscarPecasPorProduto($produtoId);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
