<?php

date_default_timezone_set('America/Sao_Paulo');

$path = session_save_path();
if (!$path) {
    $path = sys_get_temp_dir();
}

$arquivos = glob(rtrim($path, '/') . '/sess_*');

if ($arquivos === false || count($arquivos) === 0) {
	echo '[' . date('d/m/Y H:i:s') . '] ' . "Nenhuma sessão encontrada em: ". $path . "\n";
    exit;
}

$total = 0;
foreach ($arquivos as $arquivo) {
    if (@unlink($arquivo)) {
        $total++;
    }
}

echo '[' . date('d/m/Y H:i:s') . '] ' . "Sessões removidas: " .$total. " Diretório: " .$path. "\n";
