<?php

require '../../vendor/autoload.php';

use App\Controller\TipoAtendimentoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

echo json_encode(TipoAtendimentoController::listar($posto));
