<?php

use App\Auth\Autenticador;
use App\Service\UsuarioService;

$usuario = Autenticador::getUsuario();
$usuario_service = new UsuarioService();
$usuario_info = $usuario_service->buscarPorId($usuario);

if ($usuario_info["master"] === false) {
    unset($rotas['usuario']);
}

return $rotas;