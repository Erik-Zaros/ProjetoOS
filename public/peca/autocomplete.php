<?php
require '../../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Controller\PecaController;

header('Content-Type: application/json; charset=utf-8');

Autenticador::iniciar();
$posto = Autenticador::getPosto();
$term  = $_GET['term'] ?? '';

echo json_encode(PecaController::autocomplete($term, $posto));
