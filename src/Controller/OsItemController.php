<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\OsItem;
use App\Repository\OsItemRepository;

class OsItemController
{
    public static function listar($os)
    {
        return OsItemRepository::listarPorOs($os);
    }

    public static function listarListaBasica($produto)
    {
        return OsItemRepository::listarListaBasica($produto);
    }

    public static function remover($os_item, $os, $posto)
    {
        return OsItem::remover($os_item, $os, $posto);
    }

    public static function buscarPecas($termo, $produto)
    {
        return OsItemRepository::buscarPecas($termo, $produto);
    }
}
