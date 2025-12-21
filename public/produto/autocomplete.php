<?php
require '../../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Controller\ProdutoController;

header('Content-Type: application/json; charset=utf-8');

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$term  = $_GET['term'] ?? '';

echo json_encode(ProdutoController::autocomplete($term, $posto));
