<?php
require '../../vendor/autoload.php';
use App\Controller\ListaBasicaController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$produto = $_POST['produto'] ?? 0;
$peca = $_POST['peca'] ?? 0;

$result = ListaBasicaController::adicionarPeca($produto, $peca, $posto);

header('Content-Type: application/json');
echo json_encode($result);
