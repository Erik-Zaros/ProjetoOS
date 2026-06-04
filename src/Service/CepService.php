<?php

namespace App\Service;

use App\Service\Cache;

class CepService
{
    public static function buscar($cep)
    {
        if (!preg_match('/^\d{8}$/', $cep)) {
            return json_encode(['erro' => 'CEP inválido']);
        }

        $cache = new Cache('cep', $cep);

        $dados = $cache->getFromCache();

        if (!empty($dados)) {
            return $dados;
        }

        $url = "https://viacep.com.br/ws/$cep/json/";
        $response = @file_get_contents($url);

        if (!$response) {
            return json_encode([
                'erro' => 'Erro ao consultar o CEP'
            ]);
        }

        $cache->writeCache($response);

        return $response;
    }
}
