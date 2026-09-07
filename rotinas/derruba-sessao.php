<?php

$path = session_save_path();
if (!$path) {
    $path = sys_get_temp_dir();
}

$arquivos = glob(rtrim($path, '/') . '/sess_*');

if ($arquivos === false || count($arquivos) === 0) {
    echo "Nenhuma sessão encontrada em: $path\n";
    exit;
}

$total = 0;
foreach ($arquivos as $arquivo) {
    if (@unlink($arquivo)) {
        $total++;
    }
}

echo "Sessões removidas: $total\n";
echo "Diretório: $path\n";