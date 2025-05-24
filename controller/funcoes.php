<?php

if (!function_exists("buscaNomePosto")) {
    function buscaNomePosto($posto) {
        global $con;

        $sqlBuscaNomePosto = "SELECT nome as nome
                                FROM tbl_posto
                                WHERE posto = $posto
                            ";
        $resNomePosto = pg_query($con, $sqlBuscaNomePosto);

        if (pg_num_rows($resNomePosto) > 0) {
            $nomePosto = pg_fetch_result($resNomePosto, 0, 'nome');
        }
        return $nomePosto;
    }
}
