<?php
require '../../vendor/autoload.php';

use App\Controller\OsItemController;

header('Content-Type: application/json; charset=utf-8');

$termo = $_GET['term'] ?? '';
$produto = $_GET['produto'] ?? 0;

$result = OsItemController::buscarPecas($termo, $produto);
echo json_encode($result);
