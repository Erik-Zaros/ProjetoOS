<?php

namespace App\Controller\Relatorio;

use App\Core\Db;

class RelatorioPecaController
{
    public static function gerarXLS($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sqlVerifica = "SELECT COUNT(1) AS peca FROM tbl_peca WHERE posto = $posto";
        $resVerifica = pg_query($con, $sqlVerifica);

        $dados = pg_fetch_assoc($resVerifica);

        if ($dados['produto'] === 0) {
            header("Location: ../../view/peca.php?alerta=true");
        }

        $sql = " SELECT tbl_peca.codigo,
                        tbl_peca.descricao,
                        tbl_peca.ativo,
                        to_char(tbl_peca.data_input, 'DD/MM/YYYY') AS data_input
                    FROM tbl_peca
                    WHERE posto = $posto
                    ORDER BY codigo ASC
                ";
        $res = pg_query($con, $sql);

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=Relatorio_Peca.xls");
        header("Cache-Control: max-age=0");

        echo "<table border='1'>";
        echo "<tr bgcolor='#2e2e48' style='color: #ffffff; font-weight: bold;'>
                <th>Código Peça</th>
                <th>Descrição Peça</th>
                <th>Status</th>
                <th>Data Cadastro</th>
              </tr>";

        while ($row = pg_fetch_assoc($res)) {
            $ativo = $row['ativo'] === 't' ? 'Ativo' : 'Inativo';

            echo "<tr>";
            echo "<td>{$row['codigo']}</td>";
            echo "<td>{$row['descricao']}</td>";
            echo "<td>{$ativo}</td>";
            echo "<td>{$row['data_input']}</td>";
            echo "</tr>";
        }

        echo "</table>";
        exit;
    }
}
