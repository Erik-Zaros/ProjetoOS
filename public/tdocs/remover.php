<?php

require '../../vendor/autoload.php';

use App\Controller\TdocsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$tdocs = trim($_POST['tdocs']);

if (!$tdocs) {
    echo json_encode(['status' => 'error', 'message' => 'ID do anexo não informado.']);
    exit;
}

$result = TdocsController::remover($tdocs, $posto);
echo json_encode($result);
