<?php
require_once '../../vendor/autoload.php';

use App\Controller\ClienteController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$termo = $_GET['term'] ?? '';
$sugestoes = ClienteController::autocomplete($termo, $posto);

header('Content-Type: application/json');
echo json_encode($sugestoes);
