<?php

namespace App\Controller;

use App\Model\Tdocs;

class TdocsController
{
    public static function uploadTemp(array $arquivo, int $tipo_anexo, int $contexto_anexo, int $posto): array
    {
        return Tdocs::uploadTemp($arquivo, $tipo_anexo, $contexto_anexo, $posto);
    }

    public static function uploadDireto(array $arquivo, int $tipo_anexo, int $contexto_anexo, string $referencia_id, int $posto): array
    {
        return Tdocs::uploadDireto($arquivo, $tipo_anexo, $contexto_anexo, $referencia_id, $posto);
    }

    public static function vincularPorHashTemp(array $hashes, string $referencia_id, int $contexto_anexo, int $posto): bool
    {
        return Tdocs::vincularPorHashTemp($hashes, $referencia_id, $contexto_anexo, $posto);
    }

    public static function listar(string $referencia_id, int $contexto_anexo, int $posto): array
    {
        return Tdocs::listar($referencia_id, $contexto_anexo, $posto);
    }

    public static function remover(string $tdocs, int $posto): array
    {
        return Tdocs::remover($tdocs, $posto);
    }

    public static function download(string $tdocs, int $posto, bool $inline = false): void
    {
        $doc = Tdocs::buscarParaDownload($tdocs, $posto);

        if (!$doc) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Arquivo não encontrado.']);
            exit;
        }

        $caminho_abs = Tdocs::DIR_UPLOADS . $doc['caminho'];

        if (!file_exists($caminho_abs)) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Arquivo não existe no servidor.']);
            exit;
        }

        $disposicao = $inline ? 'inline' : 'attachment';
        header('Content-Type: ' . $doc['mime_type']);
        header('Content-Disposition: ' . $disposicao . '; filename="' . $doc['nome_original'] . '"');
        header('Content-Length: ' . filesize($caminho_abs));
        header('Cache-Control: private, no-cache');
        readfile($caminho_abs);
        exit;
    }
}