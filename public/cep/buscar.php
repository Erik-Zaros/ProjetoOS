<?php

require '../../vendor/autoload.php';

use App\Service\CepService;

if (isset($_POST['cep'])) {
    $cep = $_POST['cep'];
    echo CepService::buscar($cep);
}
