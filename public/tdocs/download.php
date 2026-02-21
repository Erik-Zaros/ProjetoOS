<?php

require '../../vendor/autoload.php';

use App\Controller\TdocsController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto  = Autenticador::getPosto();
$tdocs  = trim($_GET['tdocs']);
$inline = !empty($_GET['inline']);

if (!$tdocs) {
    http_response_code(400);
    echo 'ID não informado.';exit;
}

TdocsController::download($tdocs, $posto, $inline);
