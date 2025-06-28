<?php

require '../../vendor/autoload.php';

use App\Controller\ProdutoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

header('Content-Type: application/json');
echo json_encode(ProdutoController::cadastrar($_POST, $posto));
