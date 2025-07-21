<?php

require '../../vendor/autoload.php';

use App\Controller\ProdutoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$codigo = $_GET['codigo'] ?? '';
echo json_encode(ProdutoController::buscar($codigo, $posto));
