<?php
require '../../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Controller\EstoqueController;

header('Content-Type: application/json; charset=utf-8');

Autenticador::iniciar();
$posto   = Autenticador::getPosto();
$produto = $_GET['produto'] ?? null;
$peca    = $_GET['peca']    ?? null;
$limit   = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

echo json_encode(EstoqueController::listarMovimentos($posto, $produto, $peca, $limit));
