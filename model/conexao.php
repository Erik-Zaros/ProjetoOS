<?php

$localhost = 'localhost';
$username = 'root';
$password = 'pass';
$database = 'ProjetoOS';

$conn = new mysqli($localhost, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}
?>

