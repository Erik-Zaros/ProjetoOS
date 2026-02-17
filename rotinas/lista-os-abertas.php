<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Db;

$con = Db::getConnection();

date_default_timezone_set('America/Sao_Paulo');

$posto = $argv[1] ?? null;
$os = $argv[2] ?? null;

function buscaOs($posto=null, $os = null) {

	global $con;

	$campo = "";
	$join = "";
	$cond = "";
	if ($posto == null) {
		$cond = " WHERE 1=1 ";
	} else {
		$campo = ", concat(trim(tbl_produto.codigo), ' - ', trim(descricao)) as produto";
		$join = "INNER JOIN tbl_produto ON tbl_produto.produto = tbl_os.produto AND tbl_produto.posto = {$posto}";
		$cond = " WHERE tbl_os.posto = {$posto} ";
	}

	if ($os != null) {
		$cond .= " AND tbl_os.os = $os ";
	}

	$sql = "SELECT os,
				   to_char(data_abertura, 'DD/MM/YYYY') as data_abertura
				   {$campo}
			FROM tbl_os
			{$join}
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
        echo "Data de Abertura: {$dado['data_abertura']}\n";
        echo "Produto: {$dado['produto']}\n";
	}

} catch (Throwable $e) {
    echo "Erro ao Executar Rotina";
}

?>
