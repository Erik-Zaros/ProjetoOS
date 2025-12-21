<?php

use App\Auth\Autenticador;
use App\Service\FuncoesService;

$usuario = Autenticador::getUsuario();
$master = FuncoesService::usuarioMaster($usuario);

$regras = [
    'remover'  => [],
    'adicionar'=> [],
    'alterar'  => [],
];

if ($master == false) {
    $regras['remover'][] = 'usuarios';
}

return $regras;
