<?php

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

require '../../vendor/autoload.php';

use App\Controller\PecaController;
use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

echo json_encode(PecaController::listar($posto));
