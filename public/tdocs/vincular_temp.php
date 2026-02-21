<?php

require '../../vendor/autoload.php';
use App\Controller\TdocsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto          = Autenticador::getPosto();
$referencia_id  = trim($_POST['referencia_id']  ?? '');
$contexto_anexo = intval($_POST['contexto_anexo'] ?? 0);
$hashes         = json_decode($_POST['hashes'] ?? '[]', true);

if (!$referencia_id || !$contexto_anexo || !is_array($hashes)) {
    echo json_encode(['status' => 'error', 'message' => 'Parâmetros inválidos.']);
     exit;
}

TdocsController::vincularPorHashTemp($hashes, $referencia_id, $contexto_anexo, $posto);
echo json_encode(['status' => 'success']);
