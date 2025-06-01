<?php

require '../../vendor/autoload.php';

use App\Controller\OsController;

$os = $_GET['os'] ?? 0;

$result = OsController::buscar($os);
echo is_array($result) ? json_encode($result) : json_encode(['status' => 'error', 'message' => $result]);
