<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Db;

date_default_timezone_set('America/Sao_Paulo');

define('DIR_TEMP', '/var/www/html/tdocs/temp/');

function log_msg(string $msg): void {
    echo '[' . date('d/m/Y H:i:s') . '] ' . $msg . PHP_EOL;
}

function limparDirsVazios(string $dir): void
{
    if (!is_dir($dir)) return;

    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;

        $path = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            limparDirsVazios($path);

            if (count(scandir($path)) === 2) {
                rmdir($path);
            }
        }
    }
}

try {
    if (!is_dir(DIR_TEMP)) {
        log_msg("Pasta temp não encontrada: " . DIR_TEMP);
        exit(0);
    }

    $con = Db::getConnection();

    $sql = "
        SELECT tdocs, caminho
        FROM tbl_tdocs
        WHERE referencia_id IS NULL
        AND hash_temp IS NOT NULL
        AND data_input < NOW() - INTERVAL '24 hours'
        AND ativo IS TRUE
    ";
    $res = pg_query($con, $sql);
    if (!$res) {
        throw new RuntimeException('Erro na consulta: ' . pg_last_error($con));
    }

    if (pg_num_rows($res) === 0) {
        log_msg("Nenhum arquivo temporário para limpar.");
        exit(0);
    }

    $removidos = 0;
    $erros = 0;

    while ($row = pg_fetch_assoc($res)) {
        $caminho_abs = '/var/www/html/tdocs/' . $row['caminho'];
        $tdocs_id    = pg_escape_string($con, $row['tdocs']);

        if (file_exists($caminho_abs)) {
            if (!unlink($caminho_abs)) {
                log_msg("ERRO ao apagar arquivo: {$caminho_abs}");
                $erros++;
                continue;
            }
        }

        $res_delete = pg_query($con, "DELETE FROM tbl_tdocs WHERE tdocs = '{$tdocs_id}'");
        if (!$res_delete) {
            log_msg("ERRO ao excluir registro tdocs={$tdocs_id}: " . pg_last_error($con));
            $erros++;
            continue;
        }

        $removidos++;
    }

    limparDirsVazios(DIR_TEMP);

    log_msg("Limpeza concluída: {$removidos} arquivo(s) removido(s), {$erros} erro(s).");

} catch (Throwable $e) {
    log_msg("ERRO FATAL: " . $e->getMessage());
    exit(1);
}