<?php
require '../../vendor/autoload.php';

use App\Controller\OsItemController;
use App\Auth\Autenticador;

Autenticador::iniciar();
header('Content-Type: application/json; charset=utf-8');

$posto = Autenticador::getPosto();

$osItem = $_POST['os_item'] ?? null;

$result = OsItemController::remover($osItem, $posto);
echo json_encode($result);
