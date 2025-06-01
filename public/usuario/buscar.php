<?php

require '../../vendor/autoload.php';

use App\Controller\UsuarioController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$id = $_GET['usuario'] ?? null;

header('Content-Type: application/json');
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    exit;
}

$res = UsuarioController::buscar($id, $posto);
echo json_encode($res ?? ['status' => 'error', 'message' => 'Usuário não encontrado']);
