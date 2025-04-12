<?php

$con = pg_connect("host=localhost dbname=projetoos user=erikzaros password=");
if (!$con) {
    die("Erro na conexão com o PostgreSQL.");
}

?>
