<?php

namespace App\Controller\Relatorio;

use App\Core\Db;

class RelatorioServicoRealizadoController
{
    public static function gerarCSV($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sqlVerifica = "SELECT COUNT(1) AS servico_realizado FROM tbl_servico_realizado WHERE posto = $posto";
        $resVerifica = pg_query($con, $sqlVerifica);

        $dados = pg_fetch_assoc($resVerifica);

        if ($dados['servico_realizado'] === 0) {
            header("Location: ../../view/servico_realizado?alerta=true");
        }

        $sql = " SELECT tbl_servico_realizado.descricao,
                        CASE WHEN tbl_servico_realizado.ativo IS TRUE
                             THEN 'Ativo'
                             ELSE 'Inativo'
                        END AS ativo,
                        CASE WHEN tbl_servico_realizado.usa_estoque IS TRUE
                             THEN 'Sim'
                             ELSE 'Não'
                        END AS usa_estoque,
                        to_char(tbl_servico_realizado.data_input, 'DD/MM/YYYY') AS data_input
                    FROM tbl_servico_realizado
                    WHERE posto = $posto
                    ORDER BY descricao ASC
                ";
        $res = pg_query($con, $sql);


        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=relatorio_servico_realizado.csv');

        $output = fopen('php://output', 'w');

        
        $cabecalho = ['Descrição', 'Status', 'Usa Estoque', 'Data Cadastro'];
        fputcsv($output, $cabecalho, ';');

        while ($row = pg_fetch_assoc($res)) {

            fputcsv($output, [$row['descricao'], $row['ativo'], $row['usa_estoque'], $row['data_input']], ';');
        }

        fclose($output);
        exit;
    }
}
