<?php
require '../../vendor/autoload.php';
use App\Controller\ListaBasicaController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$termo = $_GET['termo'] ?? '';
$result = ListaBasicaController::buscarPecas($termo);

header('Content-Type: application/json');
echo json_encode($result);
