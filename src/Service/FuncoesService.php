<?php

namespace App\Service;

use App\Core\Db;
use App\Auth\Autenticador;

class FuncoesService
{
    public static function buscaNomePosto($postoId)
    {
        $con = Db::getConnection();
        $sql = "SELECT trim(upper(fn_retira_especiais(nome))) AS nome_posto
                FROM tbl_posto
                WHERE posto = $postoId
            ";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res) > 0) {
            $nome_posto = pg_fetch_result($res, 0, 'nome_posto');
            return $nome_posto;
        }

        return null;
    }

    public static function usuarioMaster($usuarioId)
    {
        $con   = Db::getConnection();
        $posto = Autenticador::getPosto();

        $sql = "SELECT master
                FROM tbl_usuario
                WHERE posto = $posto
                AND usuario = $usuarioId
            ";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res) > 0) {
            $master = pg_fetch_result($res, 0, 'master');

            if ($master == 't') {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    public static function validaOsFinalizadaCancelada($os)
    {
        $con   = Db::getConnection();
        $posto = Autenticador::getPosto();

        $sql = "SELECT finalizada, cancelada FROM tbl_os WHERE os = $os AND posto = $posto";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res)>0) {
            $finalizada = pg_fetch_result($res, 0, 'finalizada');
            $cancelada = pg_fetch_result($res, 0, 'cancelada');

            if ($finalizada == 't') {
                return true;
            }

            if ($cancelada == 't') {
                return true;
            }

            return false;
        }
    }

    public static function mascaraCPF($cpf) {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public static function mascaraCNPJ($cnpj) {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }

    public static function mascaraCpfCnpj($valor) {
        if (strlen($valor) === 11) {
            return FuncoesService::mascaraCPF($valor);
        }

        if (strlen($valor) === 14) {
            return FuncoesService::mascaraCNPJ($valor);
        }

        return $valor;
    }
}
