<?php
require '../../vendor/autoload.php';

use App\Controller\OsItemController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$posto = Autenticador::getPosto();

$osItem = $_POST['os_item'] ?? null;
$os = $_POST['os'] ?? null;

$result = OsItemController::remover($osItem, $os, $posto);
echo json_encode($result);
