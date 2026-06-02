<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LogAuditorRepository;

class LogAuditorController
{
    public static function buscarPorRegistro(string $tabela, $idRegistro, $posto): array
    {
        return LogAuditorRepository::buscarPorRegistro($tabela, (string) $idRegistro, (int) $posto);
    }
}
