<?php

namespace App\Auth;

class Autenticador
{
    private static $usuario;
    private static $posto;

    public static function iniciar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'], $_SESSION['posto'])) {
            header("Location: ../login.php");
            exit;
        }

        self::$usuario = $_SESSION['usuario'];
        self::$posto   = $_SESSION['posto'];
    }

    public static function getUsuario()
    {
        return self::$usuario;
    }

    public static function getPosto()
    {
        return self::$posto;
    }
}
