<?php

namespace App\Repository;

use App\Core\Db;

class IpAcessoRepository
{
    public static function registrar(int $usuario, string $ip, ?string $provedor = null): void
    {
        $con = Db::getConnection();

        $sqlCheck = "SELECT ip_acesso FROM tbl_ip_acesso
                     WHERE usuario = $1 AND ip = $2::cidr AND data::date = CURRENT_DATE
                     LIMIT 1";
        $res = pg_query_params($con, $sqlCheck, [$usuario, $ip]);

        if (pg_num_rows($res) === 0) {
            $sqlInsert = "INSERT INTO tbl_ip_acesso (usuario, ip, provedor)
                          VALUES ($1, $2::cidr, $3)";
            pg_query_params($con, $sqlInsert, [$usuario, $ip, $provedor]);
        }
    }

    public static function getIp(): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'LOCALHOST';
        $partes = explode(',', $ip);
        return trim($partes[0]);
    }
}