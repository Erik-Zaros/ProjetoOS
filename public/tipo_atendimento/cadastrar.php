<?php

require '../../vendor/autoload.php';

use App\Controller\TipoAtendimentoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

header('Content-Type: application/json');
echo json_encode(TipoAtendimentoController::cadastrar($_POST, $posto));
