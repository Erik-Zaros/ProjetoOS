<?php

use App\Auth\Autenticador;
use App\Service\FuncoesService;

$usuario = Autenticador::getUsuario();
$master = FuncoesService::usuarioMaster($usuario);

if ($master === false) {
    unset($rotas['usuario']);
}

return $rotas;