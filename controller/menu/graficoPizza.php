<?php

header('Content-Type: application/json');
include '../../model/conexao.php';

function contaQuantidadeRegistro() {

	global $conn;

	$result = [];

	$sqlClientes = "SELECT COUNT(*) AS total_clientes FROM tbl_cliente";
	$resultCLientes = $conn->query($sqlClientes);
	$rowClientes = $resultCLientes->fetch_assoc();
	$result['clientes'] = $rowClientes['total_clientes'];

	$sqlProdutos = "SELECT COUNT(*) AS total_produtos FROM tbl_produto";
	$resultProdutos = $conn->query($sqlProdutos);
	$rowProdutos = $resultProdutos->fetch_assoc();
	$result['produtos'] = $rowProdutos['total_produtos'];

	$sqlOS = "SELECT COUNT(*) AS total_os FROM tbl_os";
	$resultOS = $conn->query($sqlOS);
	$rowOS = $resultOS->fetch_assoc();
	$result['ordens_servico'] = $rowOS['total_os'];

	$sqlStatusProdutoInativo = "SELECT COUNT(*) AS produto_inativo FROM tbl_produto WHERE ativo = 0";
	$resultStatusProdutoInativo = $conn->query($sqlStatusProdutoInativo);
	if ($rowStatusProdutoInativo = $resultStatusProdutoInativo->fetch_assoc()) {
	    $result['produto_inativo'] = (int)$rowStatusProdutoInativo['produto_inativo'];
	} else {
	    $result['produto_inativo'] = 0;
	}

	$sqlStatusProdutoAtivo = "SELECT COUNT(*) AS produto_ativo FROM tbl_produto WHERE ativo = 1";
	$resultStatusProdutoAtivo = $conn->query($sqlStatusProdutoAtivo);
	if ($rowStatusProdutoAtivo = $resultStatusProdutoAtivo->fetch_assoc()) {
	    $result['produto_ativo'] = (int)$rowStatusProdutoAtivo['produto_ativo'];
	} else {
	    $result['produto_ativo'] = 0;
	}

	echo json_encode($result);
}

contaQuantidadeRegistro();

$conn->close();
?>
