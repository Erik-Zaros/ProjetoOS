<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CepService;

class CepController
{
    public static function buscar(string $cep)
    {
        return CepService::buscar($cep);
    }
}
