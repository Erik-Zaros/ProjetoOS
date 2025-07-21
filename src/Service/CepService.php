<?php

namespace App\Service;

class CepService
{
    public static function buscar($cep)
    {
        if (!preg_match('/^\d{8}$/', $cep)) {
            return json_encode(['erro' => 'CEP inválido']);
        }

        $url = "https://viacep.com.br/ws/$cep/json/";
        $response = @file_get_contents($url);

        return $response ?: json_encode(['erro' => 'Erro ao consultar o CEP']);
    }
}
