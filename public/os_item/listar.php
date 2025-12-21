<?php
require '../../vendor/autoload.php';

use App\Controller\OsItemController;

$os = $_GET['os'] ?? 0;

echo json_encode(OsItemController::listar($os));
