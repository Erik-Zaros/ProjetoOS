<?php

namespace App\Core;

class Db
{
    public static function getConnection()
    {
        $con = pg_connect("host=localhost dbname=projetoos user=erikzaros password=pass");
        if (!$con) {
            die("Erro na conexão com o banco de dados.");
        }
        return $con;
    }
}
