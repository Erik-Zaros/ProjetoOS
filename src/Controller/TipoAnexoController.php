<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TipoAnexoRepository;

class TipoAnexoController
{
    public static function listar(int $contextoAnexo): array
    {
        return TipoAnexoRepository::listarPorContexto($contextoAnexo);
    }
}
