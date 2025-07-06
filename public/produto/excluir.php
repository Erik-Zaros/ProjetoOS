<?php

require '../../vendor/autoload.php';

use App\Controller\ProdutoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$produto = $_POST['produto'] ?? 0;

$result = json_encode(ProdutoController::apagar($produto, $posto));
echo json_encode($result);
