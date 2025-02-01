<?php

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

	echo json_encode($result);
}

contaQuantidadeRegistro();

$conn->close();
?>
