<?php

	$localhost = 'localhost';
	$username = 'root';
	$password = '';
	$database = 'ProjetoOS';

	$conn = New mysqli($localhost, $username, $password, $database);

	if ($conn->connect_error) {
		die("Não foi possível conectar" . $conn->connect_error);
	}
?>
