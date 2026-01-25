<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Db;

$con = Db::getConnection();

date_default_timezone_set('America/Sao_Paulo');

$posto = $argv[1] ?? null;
$os = $argv[2] ?? null;

function buscaOs($posto=null, $os = null) {

	global $con;

	$cond = "";
	if ($posto == null) {
		$cond = " WHERE posto NOT IN (0) ";
	} else {
		$cond = " WHERE posto = {$posto} ";
	}

	if ($os != null) {
		$cond .= " AND tbl_os.os = $os ";
	}

	$sql = "SELECT os, to_char(data_abertura, 'DD/MM/YYYY') as data_abertura
			FROM tbl_os
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
        echo "Nenhuma OS encontrada";exit;
	}

	foreach ($oss as $dado) {
        echo "OS: {$dado['os']}\n";
        echo "Data Abertura: {$dado['data_abertura']}\n";
	}

} catch (Throwable $e) {
    echo "Erro ao Executar Rotina";
}

?>
