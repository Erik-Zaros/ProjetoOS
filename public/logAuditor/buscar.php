<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Model\LogAuditor;

Autenticador::iniciar();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

$tabela = $_POST['tabela'] ?? null;
$idRegistro = $_POST['id'] ?? null;

if (!$tabela || !$idRegistro) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parâmetros inválidos']);
    exit;
}

$logs = LogAuditor::buscarPorRegistro($tabela, $idRegistro);
echo json_encode(['logs' => $logs]);
