<?php

	$localhost = 'localhost';
	$username = 'root';
	$password = '';
	$database = 'ProjetoOS';

	$conn = New mysqli($localhost, $username, $password, $database);

	if ($conn->connect_error) {
		die("Não foi possível estabelecer uma conexão com o banco de dados!" . $conn->connect_error);
	}

?> 
