<?php

header('Content-Type: application/json');
include '../../model/dbconfig.php';

function contaQuantidadeRegistro() {

	global $con;

	$result = [];

	$sqlClientes = "SELECT COUNT(*) AS total_clientes FROM tbl_cliente";
	$resultClientes = pg_query($con, $sqlClientes);
	$rowClientes = pg_fetch_assoc($resultClientes);
	$result['clientes'] = $rowClientes['total_clientes'];

	$sqlProdutos = "SELECT COUNT(*) AS total_produtos FROM tbl_produto";
	$resultProdutos = pg_query($con, $sqlProdutos);
	$rowProdutos = pg_fetch_assoc($resultProdutos);
	$result['produtos'] = $rowProdutos['total_produtos'];

	$sqlOS = "SELECT COUNT(*) AS total_os FROM tbl_os";
	$resultOS = pg_query($con, $sqlOS);
	$rowOS = pg_fetch_assoc($resultOS);
	$result['ordens_servico'] = $rowOS['total_os'];

	$sqlStatusProdutoInativo = "SELECT COUNT(*) AS produto_inativo FROM tbl_produto WHERE ativo = false";
	$resultStatusProdutoInativo = pg_query($con, $sqlStatusProdutoInativo);
	if ($rowStatusProdutoInativo = pg_fetch_assoc($resultStatusProdutoInativo)) {
	    $result['produto_inativo'] = (int)$rowStatusProdutoInativo['produto_inativo'];
	} else {
	    $result['produto_inativo'] = 0;
	}

	$sqlStatusProdutoAtivo = "SELECT COUNT(*) AS produto_ativo FROM tbl_produto WHERE ativo = true";
	$resultStatusProdutoAtivo = pg_query($con, $sqlStatusProdutoAtivo);
	if ($rowStatusProdutoAtivo = pg_fetch_assoc($resultStatusProdutoAtivo)) {
	    $result['produto_ativo'] = (int)$rowStatusProdutoAtivo['produto_ativo'];
	} else {
	    $result['produto_ativo'] = 0;
	}

	echo json_encode($result);
}

contaQuantidadeRegistro();

pg_close($con);
?>