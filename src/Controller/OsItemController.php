<?php

namespace App\Controller;

use App\Model\OsItem;

class OsItemController
{
    public static function listar($os)
    {
        return OsItem::listarPorOs($os);
    }

    public static function listarListaBasica($produto)
    {
        return OsItem::listarListaBasica($produto);
    }

    public static function remover($os_item, $posto)
    {
        return OsItem::remover($os_item, $posto);
    }

    public static function buscarPecas($termo, $produto)
    {
        return OsItem::buscarPecas($termo, $produto);
    }
}
