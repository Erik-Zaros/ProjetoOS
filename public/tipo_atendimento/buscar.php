<?php

require '../../vendor/autoload.php';

use App\Controller\TipoAtendimentoController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$tipo_atendimento = $_GET['tipo_atendimento'] ?? '';
echo json_encode(TipoAtendimentoController::buscar($tipo_atendimento, $posto));
