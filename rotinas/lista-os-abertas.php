<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Db;

$con = Db::getConnection();

date_default_timezone_set('America/Sao_Paulo');

$logDir  = '/var/www/log';
if (!is_dir($logDir)) {
    mkdir($logDir, 0775, true);
}

$logFile = $logDir . '/rotina_' . date('Y-m-d_H-i-s') . '.txt';

$posto = $argv[1];
$os = $argv[2] ?? null;

function logMsg($msg) {
    global $logFile;
    file_put_contents($logFile, $msg, FILE_APPEND);
}

logMsg("Parâmetros recebidos - Posto: $posto | OS: $os");

function buscaOs($posto, $os = null) {

	global $con;

	$cond = "";
	if ($os != null) {
		$cond = " AND tbl_os.os = $os ";
	}

	$sql = "SELECT os, data_abertura
			FROM tbl_os
			WHERE posto = {$posto}
			{$cond}
		";
	$res = pg_query($con, $sql);

	if (pg_num_rows($res) > 0) {
		return pg_fetch_all($res);
	}

	return null;
}

try {

	$oss = buscaOs($posto, $os);

	if ($oss == null) {
		logMsg("Nenhuma OS encontrada");
        echo "Nenhuma OS encontrada";exit;
	}

	foreach ($oss as $dado) {
		logMsg("OS: {$dado['os']} | Data Abertura: {$dado['data_abertura']}");
        echo "OS: {$dado['os']}\n";
        echo "Data Abertura: {$dado['data_abertura']}\n";
	}

} catch (Throwable $e) {
    logMsg('Erro: ' . $e->getMessage());
    echo "Erro ao Executar Rotina";
}

?>
