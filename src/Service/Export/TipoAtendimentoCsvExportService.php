<?php

namespace App\Service\Export;

use App\Core\Db;

class TipoAtendimentoCsvExportService
{
    public function gerar($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sqlVerifica = "SELECT COUNT(1) AS tipo_atendimento FROM tbl_tipo_atendimento WHERE posto = {$posto}";
        $resVerifica = pg_query($con, $sqlVerifica);

        if (pg_num_rows($resVerifica) === 0) {
            header("Location: ../../view/tipo_atendimento?alerta=true");exit;
        }

        $sql = " SELECT tbl_tipo_atendimento.codigo,
                        tbl_tipo_atendimento.descricao,
                        CASE WHEN tbl_tipo_atendimento.ativo IS TRUE
                             THEN 'Ativo'
                             ELSE 'Inativo'
                        END AS ativo,
                        to_char(tbl_tipo_atendimento.data_input, 'DD/MM/YYYY') AS data_input
                    FROM tbl_tipo_atendimento
                    WHERE posto = $posto
                    ORDER BY codigo ASC
                ";
        $res = pg_query($con, $sql);


        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=relatorio_tipo_atendimento.csv');

        $output = fopen('php://output', 'w');

        $cabecalho = ['Código Tipo Atendimento', 'Descrição Tipo Atendimento', 'Status Tipo Atendimento', 'Data Cadastro'];
        fputcsv($output, $cabecalho, ';');

        while ($row = pg_fetch_assoc($res)) {

            fputcsv($output, [$row['codigo'], $row['descricao'], $row['ativo'], $row['data_input']], ';');
        }

        fclose($output);
        exit;
    }
}
