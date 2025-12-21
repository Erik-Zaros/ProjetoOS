<?php
require '../../vendor/autoload.php';
use App\Controller\ListaBasicaController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$id = $_POST['id'] ?? 0;
$result = ListaBasicaController::removerPeca($id);

header('Content-Type: application/json');
echo json_encode($result);
