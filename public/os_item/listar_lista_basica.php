<?php
require '../../vendor/autoload.php';

use App\Controller\OsItemController;

$produto = $_GET['produto'] ?? 0;

echo json_encode(OsItemController::listarListaBasica($produto));
