<?php

require '../../vendor/autoload.php';

use App\Controller\TdocsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$referencia_id  = trim($_GET['referencia_id']);
$contexto_anexo = intval($_GET['contexto_anexo']);

if (!$referencia_id || !$contexto_anexo) {
    echo json_encode([]);
    exit;
}

$docs = TdocsController::listar($referencia_id, $contexto_anexo, $posto);
echo json_encode($docs);
