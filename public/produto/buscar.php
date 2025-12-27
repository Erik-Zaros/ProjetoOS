<?php

require '../../vendor/autoload.php';

use App\Controller\ProdutoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$produto = $_GET['produto'] ?? '';
echo json_encode(ProdutoController::buscar($produto, $posto));
