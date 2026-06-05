<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Service\Cache;

Autenticador::iniciar();

$usuario = Autenticador::getUsuario();

header('Content-Type: application/json');

$cache = new Cache('usuario', (string) $usuario);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $raw = $cache->getFromCache();

    if ($raw === '') {
        echo json_encode(['tema' => 'light']);
        exit;
    }

    $data = json_decode($raw, true);
    die(json_encode(['tema' => $data['tema'] ?? 'light']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $tema = ($body['tema'] ?? 'light') === 'dark' ? 'dark' : 'light';

    $raw  = $cache->getFromCache();
    $data = ($raw !== '') ? (json_decode($raw, true) ?? []) : [];

    $data['tema'] = $tema;

    $ok = $cache->writeCache(json_encode($data));

    die(json_encode(['sucesso' => (bool) $ok, 'tema' => $tema]));
}

http_response_code(405);
echo json_encode(['error' => 'Método não permitido']);