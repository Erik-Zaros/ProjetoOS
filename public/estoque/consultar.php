<?php
require '../../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Controller\EstoqueController;

header('Content-Type: application/json; charset=utf-8');

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$tipo         = $_GET['tipo_item']      ?? 'ambos';
$termo        = $_GET['termo']          ?? '';
$somenteSaldo = isset($_GET['somente_saldo']);

echo json_encode(EstoqueController::consultar($posto, $tipo, $termo, $somenteSaldo));
