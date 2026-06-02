<?php

require '../../vendor/autoload.php';

use App\Controller\TipoAnexoController;
use App\Auth\Autenticador;

Autenticador::iniciar();

$contexto = (int) ($_GET['contexto_anexo'] ?? 0);

echo json_encode(TipoAnexoController::listar($contexto));
