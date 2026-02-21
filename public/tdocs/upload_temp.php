<?php

require '../../vendor/autoload.php';

use App\Controller\TdocsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$tipo_anexo     = intval($_POST['tipo_anexo']     ?? 0);
$contexto_anexo = intval($_POST['contexto_anexo'] ?? 0);

if (!$tipo_anexo || !$contexto_anexo) {
    echo json_encode(['status' => 'error', 'message' => 'Tipo ou contexto de anexo não informado.']);
    exit;
}

if (!isset($_FILES['arquivo'])) {
    echo json_encode(['status' => 'error', 'message' => 'Nenhum arquivo recebido.']);
    exit;
}

$result = TdocsController::uploadTemp($_FILES['arquivo'], $tipo_anexo, $contexto_anexo, $posto);
echo json_encode($result);