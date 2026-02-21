<?php

require '../../vendor/autoload.php';

use App\Controller\TdocsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$tipo_anexo     = intval($_POST['tipo_anexo']);
$contexto_anexo = intval($_POST['contexto_anexo']);
$referencia_id  = trim($_POST['referencia_id']);

if (!$tipo_anexo || !$contexto_anexo || !$referencia_id) {
    echo json_encode(['status' => 'error', 'message' => 'Parâmetros obrigatórios não informados.']);
    exit;
}

if (!isset($_FILES['arquivo'])) {
    echo json_encode(['status' => 'error', 'message' => 'Nenhum arquivo recebido.']);
    exit;
}

$result = TdocsController::uploadDireto($_FILES['arquivo'], $tipo_anexo, $contexto_anexo, $referencia_id, $posto);
echo json_encode($result);
