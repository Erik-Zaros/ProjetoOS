<?php

namespace App\Model;

use App\Core\Db;
use App\Auth\Autenticador;

class Tdocs
{
    const EXTENSOES_PERMITIDAS = ['pdf','jpg','jpeg','png','gif','webp','doc','docx','xls','xlsx','txt'];
    const TAMANHO_MAXIMO       = 10 * 1024 * 1024;
    const DIR_UPLOADS          = '/var/www/html/tdocs/';

    public static function uploadTemp(array $arquivo, int $tipo_anexo, int $contexto_anexo, int $posto): array
    {
        $erro = self::validaArquivo($arquivo);
        if ($erro) return ['status' => 'error', 'message' => $erro];

        $usuario     = Autenticador::getUsuario();
        $ext         = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $mime        = $arquivo['type'] ?: 'application/octet-stream';
        $nome_uuid   = self::gerarUuid() . '.' . $ext;
        $hash_temp   = bin2hex(random_bytes(16));
        $subdir      = 'temp/' . date('Y/m/');
        $caminho_rel = $subdir . $nome_uuid;
        $caminho_abs = self::DIR_UPLOADS . $caminho_rel;

        if (!is_dir(self::DIR_UPLOADS . $subdir)) {
            mkdir(self::DIR_UPLOADS . $subdir, 0755, true);
        }

        if (!move_uploaded_file($arquivo['tmp_name'], $caminho_abs)) {
            return ['status' => 'error', 'message' => 'Falha ao salvar arquivo. Verifique permissões em ' . self::DIR_UPLOADS];
        }

        $con         = Db::getConnection();
        $nome_orig   = pg_escape_string($con, $arquivo['name']);
        $nome_arq    = pg_escape_string($con, $nome_uuid);
        $ext_esc     = pg_escape_string($con, $ext);
        $mime_esc    = pg_escape_string($con, $mime);
        $tamanho     = filesize($caminho_abs);
        $cam_esc     = pg_escape_string($con, $caminho_rel);
        $hash_esc    = pg_escape_string($con, $hash_temp);

        $sql = "
            INSERT INTO tbl_tdocs
                (tipo_anexo, contexto_anexo, hash_temp, nome_original, nome_arquivo,
                 extensao, mime_type, tamanho, caminho, posto, usuario)
            VALUES
                ({$tipo_anexo}, {$contexto_anexo}, '{$hash_esc}', '{$nome_orig}', '{$nome_arq}',
                 '{$ext_esc}', '{$mime_esc}', {$tamanho}, '{$cam_esc}', {$posto}, {$usuario})
            RETURNING tdocs
        ";

        $res = pg_query($con, $sql);
        if (!$res) {
            unlink($caminho_abs);
            return ['status' => 'error', 'message' => 'Erro ao salvar no banco: ' . pg_last_error($con)];
        }

        $row = pg_fetch_assoc($res);
        return [
            'status'    => 'success',
            'message'   => 'Arquivo enviado com sucesso.',
            'tdocs'     => $row['tdocs'],
            'hash_temp' => $hash_temp,
            'nome'      => $arquivo['name'],
            'extensao'  => $ext,
        ];
    }

    public static function uploadDireto(array $arquivo, int $tipo_anexo, int $contexto_anexo, string $referencia_id, int $posto): array
    {
        $erro = self::validaArquivo($arquivo);
        if ($erro) return ['status' => 'error', 'message' => $erro];

        $usuario     = Autenticador::getUsuario();
        $ext         = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $mime        = $arquivo['type'] ?: 'application/octet-stream';
        $nome_uuid   = self::gerarUuid() . '.' . $ext;
        $subdir      = $contexto_anexo . '/' . $referencia_id . '/' . date('Y/m/');
        $caminho_rel = $subdir . $nome_uuid;
        $caminho_abs = self::DIR_UPLOADS . $caminho_rel;

        if (!is_dir(self::DIR_UPLOADS . $subdir)) {
            mkdir(self::DIR_UPLOADS . $subdir, 0755, true);
        }

        if (!move_uploaded_file($arquivo['tmp_name'], $caminho_abs)) {
            return ['status' => 'error', 'message' => 'Falha ao salvar arquivo. Verifique permissões em ' . self::DIR_UPLOADS];
        }

        $con         = Db::getConnection();
        $nome_orig   = pg_escape_string($con, $arquivo['name']);
        $nome_arq    = pg_escape_string($con, $nome_uuid);
        $ext_esc     = pg_escape_string($con, $ext);
        $mime_esc    = pg_escape_string($con, $mime);
        $tamanho     = filesize($caminho_abs);
        $cam_esc     = pg_escape_string($con, $caminho_rel);
        $ref_esc     = pg_escape_string($con, $referencia_id);

        $sql = "
            INSERT INTO tbl_tdocs
                (tipo_anexo, contexto_anexo, referencia_id, nome_original, nome_arquivo,
                 extensao, mime_type, tamanho, caminho, posto, usuario)
            VALUES
                ({$tipo_anexo}, {$contexto_anexo}, '{$ref_esc}', '{$nome_orig}', '{$nome_arq}',
                 '{$ext_esc}', '{$mime_esc}', {$tamanho}, '{$cam_esc}', {$posto}, {$usuario})
            RETURNING tdocs
        ";
        $res = pg_query($con, $sql);
        if (!$res) {
            unlink($caminho_abs);
            return ['status' => 'error', 'message' => 'Erro ao salvar no banco: ' . pg_last_error($con)];
        }

        $row = pg_fetch_assoc($res);
        return [
            'status'   => 'success',
            'message'  => 'Arquivo enviado com sucesso.',
            'tdocs'    => $row['tdocs'],
            'nome'     => $arquivo['name'],
            'extensao' => $ext,
        ];
    }

    public static function vincularPorHashTemp(array $hashes, string $referencia_id, int $contexto_anexo, int $posto): bool
    {
        if (empty($hashes)) return true;

        $con = Db::getConnection();

        foreach ($hashes as $hash) {
            $hash_esc = pg_escape_string($con, $hash);
            $ref_esc  = pg_escape_string($con, $referencia_id);

            $sqlBusca = "
                SELECT tdocs, caminho
                FROM tbl_tdocs
                WHERE hash_temp      = '{$hash_esc}'
                AND   contexto_anexo = {$contexto_anexo}
                AND   posto          = {$posto}
                AND   referencia_id  IS NULL
            ";
            $res = pg_query($con, $sqlBusca);
            if (!$res || pg_num_rows($res) === 0) continue;

            $doc      = pg_fetch_assoc($res);
            $tdocs_id = pg_escape_string($con, $doc['tdocs']);

            $novo_caminho = self::moverTemp($doc['caminho'], $referencia_id, $contexto_anexo);
            $cam_esc      = pg_escape_string($con, $novo_caminho);

            pg_query($con, "
                UPDATE tbl_tdocs
                SET referencia_id = '{$ref_esc}',
                    hash_temp     = NULL,
                    caminho       = '{$cam_esc}'
                WHERE tdocs = '{$tdocs_id}'
            ");
        }

        return true;
    }

    public static function listar(string $referencia_id, int $contexto_anexo, int $posto): array
    {
        $con     = Db::getConnection();
        $ref_esc = pg_escape_string($con, $referencia_id);

        $sql = "
            SELECT d.tdocs, d.nome_original, d.extensao, d.mime_type, d.tamanho,
                   to_char(d.data_input, 'DD/MM/YYYY HH24:MI') as data_input, ta.descricao AS tipo_descricao
            FROM tbl_tdocs d
            INNER JOIN tbl_tipo_anexo ta ON d.tipo_anexo = ta.tipo_anexo
            WHERE d.referencia_id  = '{$ref_esc}'
            AND   d.contexto_anexo = {$contexto_anexo}
            AND   d.posto          = {$posto}
            AND   d.ativo          = TRUE
            ORDER BY d.data_input DESC
        ";
        $res  = pg_query($con, $sql);
        $docs = [];
        while ($row = pg_fetch_assoc($res)) {
            $docs[] = $row;
        }
        return $docs;
    }

    public static function remover(string $tdocs, int $posto): array
    {
        $con = Db::getConnection();
        $id_esc = pg_escape_string($con, $tdocs);

		$sql = "UPDATE tbl_tdocs SET ativo = FALSE
				WHERE tdocs = '{$id_esc}' AND posto = {$posto}
			";
		$res = pg_query($con, $sql);

        if (!$res || pg_affected_rows($res) === 0) {
            return ['status' => 'error', 'message' => 'Anexo não encontrado.'];
        }

        return ['status' => 'success', 'message' => 'Anexo removido com sucesso.'];
    }

    public static function buscarParaDownload(string $tdocs, int $posto): ?array
    {
        $con    = Db::getConnection();
        $id_esc = pg_escape_string($con, $tdocs);

		$sql = "SELECT tdocs, nome_original, caminho, mime_type
				FROM tbl_tdocs
				WHERE tdocs = '{$id_esc}'
				AND posto = {$posto}
				AND ativo IS TRUE
			";
		$res = pg_query($con, $sql);

        if (!$res || pg_num_rows($res) === 0) return null;
        return pg_fetch_assoc($res);
    }

    private static function validaArquivo(array $arquivo): ?string
    {
        $erros = [
            UPLOAD_ERR_INI_SIZE   => 'O arquivo excede o limite permitido no servidor (upload_max_filesize no php.ini).',
            UPLOAD_ERR_FORM_SIZE  => 'O arquivo excede o tamanho máximo definido no formulário.',
            UPLOAD_ERR_PARTIAL    => 'O upload foi interrompido, tente novamente.',
            UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo foi enviado.',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausente no servidor.',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar arquivo temporário em disco.',
        ];

        $code = $arquivo['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($code !== UPLOAD_ERR_OK) {
            return $erros[$code] ?? 'Erro desconhecido no upload (código ' . $code . ').';
        }

        if ($arquivo['size'] > self::TAMANHO_MAXIMO) {
            return 'O arquivo excede o tamanho máximo de 10 MB.';
        }

        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::EXTENSOES_PERMITIDAS)) {
            return "Extensão '.' . $ext . '' não é permitida.";
        }

        return null;
    }

    private static function moverTemp(string $caminho_atual, string $referencia_id, int $contexto_anexo): string
    {
        $nome_arquivo = basename($caminho_atual);
        $novo_subdir  = $contexto_anexo . '/' . $referencia_id . '/' . date('Y/m/');
        $novo_caminho = $novo_subdir . $nome_arquivo;
        $abs_origem   = self::DIR_UPLOADS . $caminho_atual;
        $abs_destino  = self::DIR_UPLOADS . $novo_caminho;

        if (!is_dir(self::DIR_UPLOADS . $novo_subdir)) {
            mkdir(self::DIR_UPLOADS . $novo_subdir, 0755, true);
        }

        if (file_exists($abs_origem)) {
            rename($abs_origem, $abs_destino);
        }

        return $novo_caminho;
    }

    private static function gerarUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
