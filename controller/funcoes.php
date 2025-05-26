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

if (!function_exists("verificaOsFinalizada")) {
    function verificaOsFinalizada($os, $posto) {
        global $con;

        $sqlVerificaOsFinalizada = "SELECT os,
                                           finalizada as finalizada
                                        FROM tbl_os
                                        WHERE os = $os
                                        AND posto = $posto
                                    ";
        $resVerificaOsFinalizada = pg_query($con, $sqlVerificaOsFinalizada);

        if (pg_num_rows($resVerificaOsFinalizada) > 0) {
            $osFinalizada = pg_fetch_result($resVerificaOsFinalizada, 0, 'finalizada');
        }
        return $osFinalizada;
    }
}
