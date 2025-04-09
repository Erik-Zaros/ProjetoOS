<?php

function buscaCep($cep) {
    if (preg_match('/^\d{8}$/', $cep)) {
        $url = "https://viacep.com.br/ws/$cep/json/";
        $response = file_get_contents($url);
        return $response;
    } else {
        return json_encode(['erro' => 'CEP inválido']);
    }
}

if (isset($_POST['cep'])) {
    $cep = $_POST['cep'];
    echo buscaCep($cep);
}

?>