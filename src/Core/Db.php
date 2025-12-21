<?php

namespace App\Core;

use Dotenv\Dotenv;

class Db
{
    private static $con;

    public static function getConnection()
    {
        if (!self::$con) {

            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();

            $host = $_ENV['DB_HOST'];
            $db   = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

            $connStr = "host=$host dbname=$db user=$user password=$pass";

            self::$con = pg_connect($connStr);

            if (!self::$con) {
                die("Erro ao conectar no banco.");
            }
        }

        return self::$con;
    }
}
