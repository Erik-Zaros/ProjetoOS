<?php

namespace App\Service\Export;

use App\Core\Db;

class PecaCsvExportService
{
    public function gerar($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sqlVerifica = "SELECT COUNT(1) AS peca FROM tbl_peca WHERE posto = {$posto}";
        $resVerifica = pg_query($con, $sqlVerifica);

        if (pg_num_rows($resVerifica) === 0) {
            header("Location: ../../view/peca.php?alerta=true");exit;
        }

        $sql = " SELECT tbl_peca.codigo,
                        tbl_peca.descricao,
                        CASE WHEN tbl_peca.ativo IS TRUE
                             THEN 'Ativo'
                             ELSE 'Inativo'
                        END AS ativo,
                        to_char(tbl_peca.data_input, 'DD/MM/YYYY') AS data_input
                    FROM tbl_peca
                    WHERE posto = $posto
                    ORDER BY codigo ASC
                ";
        $res = pg_query($con, $sql);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=relatorio_peca.csv');

        $output = fopen('php://output', 'w');

        $cabecalho = ['Código Peça', 'Descrição Peça', 'Status Peça', 'Data Cadastro'];
        fputcsv($output, $cabecalho, ';');

        while ($row = pg_fetch_assoc($res)) {

            fputcsv($output, [$row['codigo'], $row['descricao'], $row['ativo'], $row['data_input']], ';');
        }

        fclose($output);
        exit;
    }
}
