<?php

require '../../vendor/autoload.php';

use App\Controller\CepController;

if (isset($_POST['cep'])) {
    echo CepController::buscar($_POST['cep']);
}
