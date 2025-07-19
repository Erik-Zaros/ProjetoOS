<?php

namespace App\Controller\Relatorio;

use App\Core\Db;

class RelatorioPecaController
{
    public static function gerarCSV($posto)
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

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=Relatorio_Peça.csv');

        $output = fopen('php://output', 'w');

        $cabecalho = ['Código Peça', 'Descrição Peça', 'Status', 'Data Cadastro'];
        fputcsv($output, $cabecalho, ';');

        while ($row = pg_fetch_assoc($res)) {
            $ativo = $row['ativo'] === 't' ? 'Ativo' : 'Inativo';

            fputcsv($output, [
                $row['codigo'], $row['descricao'], $ativo,
                $row['data_input']
            ], ';');
        }

        fclose($output);
        exit;
    }
}
